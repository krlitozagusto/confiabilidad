<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use stdClass;
use Carbon\Carbon;

class ReportesController extends Controller
{
    function check_date_range ($rdi, $rdf, $rri, $rrf) {
        $rdi = strtotime($rdi);
        $rdf = strtotime($rdf);
        $rri = strtotime($rri);
        $rrf = strtotime($rrf);
        return ($rri >= $rdi && $rri <= $rdf) || ($rrf >= $rdi && $rrf <= $rdf) || ($rdi >= $rri && $rdi <= $rrf) || ($rdf >= $rri && $rdf <= $rrf);
    }

    public function tiempoMedio (Request $request) {
        $requestjson = json_decode($request->getContent());
        $requestjson->fechaInicio = new Carbon($requestjson->fechaInicio);
        $requestjson->fechaFin = new Carbon($requestjson->fechaFin);
        $requestjson->fechaFin = $requestjson->fechaFin->add(1, 'day');
        $minutos_totales = $requestjson->fechaFin->diffInMinutes($requestjson->fechaInicio);
        $eventos = Evento::where('equipo_id', '=', $requestjson->equipo_id)->with('tipo_evento')->get();
        $registros = [];
        $total_minutos_falla_equipo = 0;
        $eventos_calculados = 0;
        foreach ($eventos as $evento) {
            if($this->check_date_range($requestjson->fechaInicio, $requestjson->fechaFin, $evento->fecha_inicio, $evento->fecha_fin)) {
                $eventos_calculados++;
                $registro =  new stdClass();
                //fallando
                $copia_fecha_inicio = $evento->fecha_inicio ? new Carbon($evento->fecha_inicio) : $requestjson->fechaInicio;
                $copia_fecha_fin = $evento->fecha_fin ? new Carbon($evento->fecha_fin) : new Carbon();
                $evento->calculo_falla_fecha_inicio = ($requestjson->fechaInicio->diffInMinutes($copia_fecha_inicio, false) >= 0) ? $copia_fecha_inicio : $requestjson->fechaInicio;
                $evento->calculo_falla_fecha_final = ($requestjson->fechaFin->diffInMinutes($copia_fecha_fin, false) <= 0) ? $copia_fecha_fin : $requestjson->fechaFin;
                $evento->minutos_total_falla = $evento->calculo_falla_fecha_inicio->diffInMinutes($evento->calculo_falla_fecha_final);
                $total_minutos_falla_equipo = $total_minutos_falla_equipo + $evento->minutos_total_falla;
                $evento->horas_falla = intval(($evento->minutos_total_falla / 60));
                $evento->minutos_falla = ($evento->minutos_total_falla - ($evento->horas_falla * 60));
                $evento->tiempo_falla = "{$evento->horas_falla}H : {$evento->minutos_falla}M";
                
                $registro->evento = $evento;
                $registro->porcentaje_individual = round((($registro->evento->minutos_total_falla * 100) / $minutos_totales), 2);
                array_push($registros, $registro);
            }
        }
        $objeto =  new stdClass();
        $objeto->registros = $registros;
        $objeto->eventos_calculados = $eventos_calculados;
        $objeto->minutos_intervalo = $minutos_totales;
        //tiempo total intervalo
        $total_horas_intervalo = intval(($objeto->minutos_intervalo / 60));
        $total_minutos_intervalo = ($objeto->minutos_intervalo - ($total_horas_intervalo * 60));
        $objeto->total_tiempo_intervalo = "{$total_horas_intervalo}H";

        $objeto->minutos_falla = $total_minutos_falla_equipo;
        //tiempo total falla
        $total_horas_falla = intval(($objeto->minutos_falla / 60));
        $total_minutos_falla = ($objeto->minutos_falla - ($total_horas_falla * 60));
        $objeto->total_tiempo_falla = "{$total_horas_falla}H : {$total_minutos_falla}M";
        $objeto->porcentaje_minutos_falla = round((($objeto->minutos_falla * 100) / $objeto->minutos_intervalo), 2);
        return response()->json($objeto, 200);
    }
}
