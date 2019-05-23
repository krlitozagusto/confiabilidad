<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Carbon\Carbon;

class ReportesController extends Controller
{
    public function tiempoMedio (Request $request) {
        $requestjson = json_decode($request->getContent());
        $requestjson->fechaInicio = new Carbon($requestjson->fechaInicio);
        $requestjson->fechaFin = new Carbon($requestjson->fechaFin);
        $minutos_totales = $requestjson->fechaFin->diffInMinutes($requestjson->fechaInicio);
        $eventos = Evento::where('equipo_id', '=', $requestjson->equipo_id)->get();
        $eventos->each(function ($item, $key) use($requestjson) {
            $limite_inicio =
            dd($item->fecha_inicio);
        });

        return response()->json([
            'registros'=> 'los registros'
        ], 200);
    }
}
