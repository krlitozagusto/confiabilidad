<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Evento;
use App\Models\Sistema;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use stdClass;
use Carbon\Carbon;

class resultTime {
    public $total_minutos = 0;
    public $total_horas = 0;
    public $horas = 0;
    public $minutos = 0;
}

class disponibilidad {
    public $intervalo;
    public $disponibilidad = '100%';
    public $registros = [];
    public $falla;
    public $fallas = 0;
    public $reparacion;
    public $reparaciones = 0;
    public $mtbf;
    public $mttr;
}

class ReportesController extends Controller
{
//    $rdi: Rango dado inicial
//    $rdf: Rango dado final
//    $rri: Rango registro inicial
//    $rdf: Rango registro final
    function check_date_range ($rdi, $rdf, $rri, $rrf) {
        $rdi = strtotime($rdi);
        $rdf = strtotime($rdf);
        $rri = strtotime($rri);
        $rrf = strtotime($rrf);
        return ($rri >= $rdi && $rri <= $rdf) || ($rrf >= $rdi && $rrf <= $rdf) || ($rdi >= $rri && $rdi <= $rrf) || ($rdf >= $rri && $rdf <= $rrf);
    }

    function objectTime ($total_minutos) {
        $result = new resultTime();
        if ($total_minutos > 0) {
            $result->total_minutos = $total_minutos;
            $result->total_horas = round(($result->total_minutos / 60), 2);
            $result->horas = intval(($result->total_minutos / 60));
            $result->minutos = ($result->total_minutos - ($result->horas * 60));
        }
        return $result;
    }

    public function tiempoMedio (Request $request) {
        $requestjson = json_decode($request->getContent());
        $requestjson->fechaInicio = new Carbon($requestjson->fechaInicio);
        $requestjson->fechaFin = new Carbon($requestjson->fechaFin);
        $requestjson->fechaFin = $requestjson->fechaFin->add(1, 'day');
        $response = new stdClass();
        $response->data = [];
        switch ($requestjson->tipoTaxonomia) {
            case 'Equipo': {
                $response->equipo = Equipo::where('id', '=', $requestjson->taxonomia_id)->first();
                $response->data = $this->disponibilidadRangoEquipo($requestjson);
                break;
            }
            case 'Sistema': {
                $response->sistema = Sistema::where('id', '=', $requestjson->taxonomia_id)->first();
                $response->data = $this->disponibilidadRangoSistema($requestjson);
                break;
            }
        }
        return response()->json($response, 200);
    }

    public function disponibilidadRangoSistema ($requestjson) {
        $equipos = Equipo::where('sistema_id', '=', $requestjson->taxonomia_id)->get();
        $objeto =  new disponibilidad();
        $minutos_falla = 0;
        $minutos_reparacion = 0;
        $minutos_intervalo = 0;
        foreach ($equipos as $equipo) {
            $response_equipo = new stdClass();
            $response_equipo->equipo = Equipo::where('id', '=', $equipo->id)->first();
            $requestjson->taxonomia_id = $equipo->id;
            $response_equipo->data = $this->disponibilidadRangoEquipo($requestjson);
            $minutos_falla = $minutos_falla + ($response_equipo->data->falla ? $response_equipo->data->falla->total_minutos : 0);
            $minutos_reparacion = $minutos_reparacion + ($response_equipo->data->reparacion ? $response_equipo->data->reparacion->total_minutos : 0);
            $objeto->fallas = $objeto->fallas + $response_equipo->data->fallas;
            $objeto->reparaciones = $objeto->reparaciones + $response_equipo->data->reparaciones;
            $minutos_intervalo = $response_equipo->data->intervalo->total_minutos;
            array_push($objeto->registros, $response_equipo);
        }
        $objeto->intervalo = $this->objectTime($minutos_intervalo);
        $objeto->falla = $this->objectTime($minutos_falla);
        $objeto->reparacion = $this->objectTime($minutos_reparacion);
        //Disponibilidad
        $objeto->disponibilidad = $this->calculaDisponibilidad($objeto->intervalo->total_minutos, $objeto->falla->total_minutos);
        //Tiempo medio entre fallas
        $objeto->mtbf = $this->calculaMtbf($objeto->falla->total_minutos, $objeto->fallas);
        //Tiempo medio entre reparaciones
        $objeto->mttr = $this->calculaMttr($objeto->reparacion->total_minutos, $objeto->reparaciones);
        return $objeto;
    }

    public function disponibilidadRangoEquipo ($requestjson) {
        $objeto =  new disponibilidad();
        $eventos = Evento::where('equipo_id', '=', $requestjson->taxonomia_id)->with('tipo_evento')->get();
        //tiempo total intervalo
        $objeto->intervalo = $this->objectTime($requestjson->fechaFin->diffInMinutes($requestjson->fechaInicio));
        if ($eventos->count()) {
            $total_minutos_falla_equipo = 0;
            $total_minutos_reparacion_equipo = 0;
            foreach ($eventos as $evento) {
                if($this->check_date_range($requestjson->fechaInicio, $requestjson->fechaFin, $evento->fecha_inicio, $evento->fecha_fin)) {
                    $objeto->fallas++;
                    //fallando
                    $copia_fecha_inicio = $evento->fecha_inicio ? new Carbon($evento->fecha_inicio) : $requestjson->fechaInicio;
                    $copia_fecha_fin = $evento->fecha_fin ? new Carbon($evento->fecha_fin) : new Carbon();
                    $calculo_fecha_inicio = ($requestjson->fechaInicio->diffInMinutes($copia_fecha_inicio, false) >= 0) ? $copia_fecha_inicio : $requestjson->fechaInicio;
                    $calculo_fecha_final = ($requestjson->fechaFin->diffInMinutes($copia_fecha_fin, false) <= 0) ? $copia_fecha_fin : $requestjson->fechaFin;

                    $evento->falla = $this->objectTime($calculo_fecha_inicio->diffInMinutes($calculo_fecha_final));
                    $total_minutos_falla_equipo = $total_minutos_falla_equipo + $evento->falla->total_minutos;
                    //reparación
                    $evento->reparacion = $this->objectTime(0);
                    if($this->check_date_range($requestjson->fechaInicio, $requestjson->fechaFin, $evento->fecha_inicio_reparacion, $evento->fecha_fin_reparacion)) {
                        $objeto->reparaciones++;
                        $copia_fecha_inicio = $evento->fecha_inicio_reparacion ? new Carbon($evento->fecha_inicio_reparacion) : $requestjson->fechaInicio;
                        $copia_fecha_fin = $evento->fecha_fin_reparacion ? new Carbon($evento->fecha_fin_reparacion) : new Carbon();
                        $calculo_fecha_inicio = ($requestjson->fechaInicio->diffInMinutes($copia_fecha_inicio, false) >= 0) ? $copia_fecha_inicio : $requestjson->fechaInicio;
                        $calculo_fecha_final = ($requestjson->fechaFin->diffInMinutes($copia_fecha_fin, false) <= 0) ? $copia_fecha_fin : $requestjson->fechaFin;

                        $evento->reparacion = $this->objectTime($calculo_fecha_inicio->diffInMinutes($calculo_fecha_final));
                        $total_minutos_reparacion_equipo = $total_minutos_reparacion_equipo + $evento->reparacion->total_minutos;
                    }
                    array_push($objeto->registros, $evento);
                }
            }
            if ($objeto->fallas) {
                //Disponibilidad
                $objeto->disponibilidad = $this->calculaDisponibilidad($objeto->intervalo->total_minutos, $total_minutos_falla_equipo);
                //Tiempo medio entre fallas
                $objeto->mtbf = $this->calculaMtbf($total_minutos_falla_equipo, $objeto->fallas);

                //tiempo total fallas
                $objeto->falla = $this->objectTime($total_minutos_falla_equipo);

                if ($objeto->reparaciones) {
                    //Tiempo medio entre reparaciones
                    $objeto->mttr = $this->calculaMttr($total_minutos_reparacion_equipo, $objeto->reparaciones);

                    //tiempo total reparaciones
                    $objeto->reparacion = $this->objectTime($total_minutos_reparacion_equipo);
                }
            }
        }
        return $objeto;
    }

    public function calculaMttr ($minutos_reparacion, $cantidad_reparaciones) {
        return $this->objectTime(round(($minutos_reparacion / $cantidad_reparaciones), 2));
    }

    public function calculaMtbf ($minutos_falla, $cantidad_fallas) {
        return $this->objectTime(round(($minutos_falla / $cantidad_fallas), 2));
    }

    public function calculaDisponibilidad ($minutos_intervalo, $minutos_falla) {
        return round(((($minutos_intervalo - $minutos_falla) / $minutos_intervalo) * 100), 2).'%';
    }
}
