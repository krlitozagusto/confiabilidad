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
    public function tiempoMedio (Request $request) {
        $requestjson = json_decode($request->getContent());
        $requestjson->fechaInicio = new Carbon($requestjson->fechaInicio);
        $requestjson->fechaFin = new Carbon($requestjson->fechaFin);
        $minutos_totales = $requestjson->fechaFin->diffInMinutes($requestjson->fechaInicio);
        $eventos = Evento::where('equipo_id', '=', $requestjson->equipo_id)->get();
        $data = [];
        foreach ($eventos as $evento) {
            $evento->fecha_inicio = new Carbon($evento->fecha_inicio);
            $evento->fecha_fin = new Carbon($evento->fecha_fin);
            $limite_inicio = ($requestjson->fechaInicio->diffInMinutes($evento->fecha_inicio, false) >= 0) ? $evento->fecha_inicio : $requestjson->fechaInicio;
            $limite_final = ($requestjson->fechaFin->diffInMinutes($evento->fecha_fin, false) <= 0) ? $evento->fecha_fin : $requestjson->fechaFin;
            $minutos_fallando = $limite_inicio->diffInMinutes($limite_final);
            array_push($data, $minutos_fallando);
        }
        $objeto =  new stdClass();
        $objeto->data = $data;
        dd($objeto->data);
        return response()->json([
            'registros'=> 'los registros'
        ], 200);
    }
}
