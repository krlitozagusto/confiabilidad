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
    public $registros_fallas = [];
    public $fallas;
    public $reparacion;
    public $mtbf;
    public $mttr;

    public $total_tiempo_mtbf = "0H : 0M";
    public $minutos_mtbf = 0;
    public $total_tiempo_mttr = "0H : 0M";
    public $minutos_mttr = 0;
    public $total_tiempo_falla = "0H : 0M";
    public $minutos_falla = 0;
    public $total_tiempo_reparacion = "0H : 0M";
    public $minutos_reparacion = 0;
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
        $eventos = Evento::where('equipo_id', '=', $requestjson->equipo_id)->with('tipo_evento')->get();
        $objeto =  new disponibilidad();
        //tiempo total intervalo
        $objeto->intervalo = $this->objectTime($requestjson->fechaFin->diffInMinutes($requestjson->fechaInicio));
        if ($eventos->count()) {
            $registros_fallas = [];
            $total_minutos_falla_equipo = 0;
            $eventos_calculados_fallas = 0;
            $total_minutos_reparacion_equipo = 0;
            $eventos_calculados_reparaciones = 0;
            foreach ($eventos as $evento) {
                if($this->check_date_range($requestjson->fechaInicio, $requestjson->fechaFin, $evento->fecha_inicio, $evento->fecha_fin)) {
                    $eventos_calculados_fallas++;
                    //fallando
                    $copia_fecha_inicio = $evento->fecha_inicio ? new Carbon($evento->fecha_inicio) : $requestjson->fechaInicio;
                    $copia_fecha_fin = $evento->fecha_fin ? new Carbon($evento->fecha_fin) : new Carbon();
                    $calculo_fecha_inicio = ($requestjson->fechaInicio->diffInMinutes($copia_fecha_inicio, false) >= 0) ? $copia_fecha_inicio : $requestjson->fechaInicio;
                    $calculo_fecha_final = ($requestjson->fechaFin->diffInMinutes($copia_fecha_fin, false) <= 0) ? $copia_fecha_fin : $requestjson->fechaFin;
                    $minutos_total = $calculo_fecha_inicio->diffInMinutes($calculo_fecha_final);
                    $total_minutos_falla_equipo = $total_minutos_falla_equipo + $minutos_total;
                    $evento->horas_falla = intval(($minutos_total / 60));
                    $evento->minutos_falla = ($minutos_total - ($evento->horas_falla * 60));
                    $evento->tiempo_falla = "{$evento->horas_falla}H : {$evento->minutos_falla}M";
                    //reparación
                    $evento->horas_reparacion = 0;
                    $evento->minutos_reparacion = 0;
                    $evento->tiempo_reparacion = "0H : 0M";
                    if($this->check_date_range($requestjson->fechaInicio, $requestjson->fechaFin, $evento->fecha_inicio_reparacion, $evento->fecha_fin_reparacion)) {
                        $eventos_calculados_reparaciones++;
                        $copia_fecha_inicio = $evento->fecha_inicio_reparacion ? new Carbon($evento->fecha_inicio_reparacion) : $requestjson->fechaInicio;
                        $copia_fecha_fin = $evento->fecha_fin_reparacion ? new Carbon($evento->fecha_fin_reparacion) : new Carbon();
                        $calculo_fecha_inicio = ($requestjson->fechaInicio->diffInMinutes($copia_fecha_inicio, false) >= 0) ? $copia_fecha_inicio : $requestjson->fechaInicio;
                        $calculo_fecha_final = ($requestjson->fechaFin->diffInMinutes($copia_fecha_fin, false) <= 0) ? $copia_fecha_fin : $requestjson->fechaFin;
                        $minutos_total = $calculo_fecha_inicio->diffInMinutes($calculo_fecha_final);
                        $total_minutos_reparacion_equipo = $total_minutos_reparacion_equipo + $minutos_total;
                        $evento->horas_reparacion = intval(($minutos_total / 60));
                        $evento->minutos_reparacion = ($minutos_total - ($evento->horas_reparacion * 60));
                        $evento->tiempo_reparacion = "{$evento->horas_reparacion}H : {$evento->minutos_reparacion}M";
                    }
                    array_push($registros_fallas, $evento);
                }
            }
            if ($eventos_calculados_fallas) {
                //eventos
                $objeto->registros_fallas = $registros_fallas;
                //Disponibilidad
                $objeto->disponibilidad = round(((($objeto->intervalo->total_minutos - $total_minutos_falla_equipo) / $objeto->intervalo->total_minutos) * 100), 2).'%';
                //Tiempo medio entre fallas
                $minutos_totales_mtbf = round(($total_minutos_falla_equipo / $eventos_calculados_fallas), 2);
                $horas__mtbf = intval(($minutos_totales_mtbf / 60));
                $minutos__mtbf = ($minutos_totales_mtbf - ($horas__mtbf * 60));
                $objeto->total_tiempo_mtbf = "{$horas__mtbf}H : {$minutos__mtbf}M";
                $objeto->minutos_mtbf = $minutos_totales_mtbf;

                //tiempo total falla
                $objeto->minutos_falla = $total_minutos_falla_equipo;
                $total_horas_falla = intval(($objeto->minutos_falla / 60));
                $total_minutos_falla = ($objeto->minutos_falla - ($total_horas_falla * 60));
                $objeto->total_tiempo_falla = "{$total_horas_falla}H : {$total_minutos_falla}M";

                if ($eventos_calculados_reparaciones) {
                    //Tiempo medio entre reparaciones
                    $minutos_totales_mttr = round(($total_minutos_reparacion_equipo / $eventos_calculados_reparaciones), 2);
                    $horas_mttr = intval(($minutos_totales_mttr / 60));
                    $minutos_mttr = ($minutos_totales_mttr - ($horas_mttr * 60));
                    $objeto->total_tiempo_mttr = "{$horas_mttr}H : {$minutos_mttr}M";
                    $objeto->minutos_mttr = $minutos_totales_mttr;

                    //tiempo total reparacion
                    $objeto->minutos_reparacion = $total_minutos_reparacion_equipo;
                    $total_horas_reparacion = intval(($objeto->minutos_reparacion / 60));
                    $total_minutos_reparacion = ($objeto->minutos_reparacion - ($total_horas_reparacion * 60));
                    $objeto->total_tiempo_reparacion = "{$total_horas_reparacion}H : {$total_minutos_reparacion}M";
                }
            }
        }
        return response()->json($objeto, 200);
    }
}
