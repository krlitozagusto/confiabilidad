<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Evento;
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
    public $registros_eventos = [];
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
        switch ($requestjson->taxonomia) {
            case 'equipo': {
                $response = $this->disponibilidadRangoEquipo.($requestjson);
                break;
            }
            case 'sistema': {
                $response = $this->disponibilidadRangoSistema.($requestjson);
                break;
            }
        }
        return response()->json($response, 200);
    }

    public function disponibilidadRangoSistema ($requestjson) {

    }

    public function disponibilidadRangoEquipo ($requestjson) {
        $objeto =  new disponibilidad();
        $eventos = Evento::where('equipo_id', '=', $requestjson->equipo_id)->with('tipo_evento')->get();
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
                    array_push($objeto->registros_eventos, $evento);
                }
            }
            if ($objeto->fallas) {
                //Disponibilidad
                $objeto->disponibilidad = round(((($objeto->intervalo->total_minutos - $total_minutos_falla_equipo) / $objeto->intervalo->total_minutos) * 100), 2).'%';
                //Tiempo medio entre fallas
                $objeto->mtbf = $this->objectTime(round(($total_minutos_falla_equipo / $objeto->fallas), 2));

                //tiempo total fallas
                $objeto->falla = $this->objectTime($total_minutos_falla_equipo);

                if ($objeto->reparaciones) {
                    //Tiempo medio entre reparaciones
                    $objeto->mttr = $this->objectTime(round(($total_minutos_reparacion_equipo / $objeto->reparaciones), 2));

                    //tiempo total reparaciones
                    $objeto->reparacion = $this->objectTime($total_minutos_reparacion_equipo);
                }
            }
        }
        return $objeto;
    }
}
