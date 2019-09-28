<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Evento;
use App\Models\Planta;
use App\Models\Sistema;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use function foo\func;
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
    public $fecha_inicial;
    public $fecha_final;
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
            $result->minutos = round(($result->total_minutos - ($result->horas * 60)),2);
        }
        return $result;
    }

    public function listaEventos (Request $request) {
        $requestjson = json_decode($request->getContent());
        $requestjson->fechaInicio = new Carbon(($requestjson->fechaInicio.' '.$requestjson->horaInicio));
        $requestjson->fechaFin = new Carbon(($requestjson->fechaFin.' '.$requestjson->horaFin));
        $eventosfiltrados = $this->filtraEventos($requestjson);
        return response()->json([
            'eventos' => $eventosfiltrados
        ], 200);
    }

    public function filtraEventos ($requestjson) {
        $idsEquipos = [];
        switch ($requestjson->tipoTaxonomia) {
            case 'Equipo': {
                array_push($idsEquipos, $requestjson->taxonomia_id);
                break;
            }
            case 'Sistema': {
                $equipos = Equipo::where('sistema_id', '=', $requestjson->taxonomia_id)->select('id')->get();
                foreach ($equipos as $equipo) {
                    array_push($idsEquipos, $equipo->id);
                }
                break;
            }
            case 'Planta': {
                $equipos = Equipo::where(function($query) use ($requestjson) {
                    $query->whereHas('sistema',function ($query) use ($requestjson) {
                        $query->whereHas('planta',function ($query) use ($requestjson) {
                            $query->where('id', '=' , $requestjson->taxonomia_id);
                        });
                    });
                })->select('id')->get();
                foreach ($equipos as $equipo) {
                    array_push($idsEquipos, $equipo->id);
                }
                break;
            }
        }
        $eventos = Evento::where(function($query) use ($requestjson) {
            if ($requestjson->contractual) {
                $query->where('contractual', '=', 1);
            }
        })->whereIn('equipo_id',$idsEquipos)
            ->whereIn('tipo_evento_id', $requestjson->tipoEvento)
            ->with([
                'tipo_evento',
                'tipo_mantenimiento',
                'comentarios.user',
                'fallas.modo_falla',
                'orden_trabajos.puesto_trabajo',
                'gastos',
                'impactos',
                'user',
                'equipo.valoracion_ram',
                'equipo.sistema.planta.campo.contrato'
            ])->get();

        $eventosRango = [];
        if ($eventos->count()) {
            foreach ($eventos as $evento) {
                if($this->check_date_range($requestjson->fechaInicio, $requestjson->fechaFin, $evento->fecha_inicio, $evento->fecha_fin)) {
                    //fechas
                    $copia_fecha_inicio = $evento->fecha_inicio ? new Carbon($evento->fecha_inicio) : $requestjson->fechaInicio;
                    $copia_fecha_fin = $evento->fecha_fin ? new Carbon($evento->fecha_fin) : new Carbon();
                    $diferencia_fecha_inicio = $requestjson->fechaInicio->diffInMinutes($copia_fecha_inicio, false);
                    $diferencia_fecha_fin = $requestjson->fechaFin->diffInMinutes($copia_fecha_fin, false);

                    $calculo_fecha_inicio = (($diferencia_fecha_inicio === 0) ? $requestjson->fechaInicio : ($diferencia_fecha_inicio < 0) && ($requestjson->fechaInicio->diffInMinutes($copia_fecha_fin, false))) ? $requestjson->fechaInicio : $copia_fecha_inicio;
                    $calculo_fecha_fin = ($diferencia_fecha_fin === 0) ? $requestjson->fechaFin : ($diferencia_fecha_fin < 0) ? $copia_fecha_fin : $requestjson->fechaFin;

                    $evento->fecha_inicio = $calculo_fecha_inicio->toDateTimeString();
                    $evento->fecha_fin = $calculo_fecha_fin->toDateTimeString();
                    if (($calculo_fecha_inicio->diffInMinutes($calculo_fecha_fin, false) !== 0) && ($calculo_fecha_inicio->diffInMinutes($requestjson->fechaInicio, false) <= 0)) array_push($eventosRango, $evento);
                }
            }
        }
        return $eventosRango;
    }

    public function tiempoMedio (Request $request) {
        $requestjson = json_decode($request->getContent());
        $requestjson->fechaInicio = new Carbon(($requestjson->fechaInicio.' '.$requestjson->horaInicio));
        $requestjson->fechaFin = new Carbon(($requestjson->fechaFin.' '.$requestjson->horaFin));
        $response = new stdClass();
        $response -> eventos = $this->filtraEventos($requestjson);
        $response->data = [];
        if (!$requestjson->rangos) {
//            $requestjson->fechaFin = $requestjson->fechaFin->add(1, 'day');
//            $requestjson->frecuencia = $requestjson->fechaInicio->diffInDays($requestjson->fechaFin, false).' days';
            $rango = $this->singleData($requestjson);
            if ($rango->data) {
                $rango->fecha_inicial = $requestjson->fechaInicio->format('Y-m-d H:i');
                $rango->fecha_final = $requestjson->fechaFin->format('Y-m-d H:i');
                array_push($response->data, $rango);
            }
//            dd($requestjson->frecuencia);
//            $requestjson->fechaFin = $requestjson->fechaFin->add(1, 'day');
//            $response = $this->singleData($requestjson);
//            return response()->json($response, 200);
        } else {
            $period = CarbonPeriod::create($requestjson->fechaInicio, $requestjson->frecuencia, $requestjson->fechaFin);
            $period = $period->toArray();
            for($i = 1; $i < count($period); $i++){
                if ($requestjson->tipoResultado === "Periódico") $requestjson->fechaInicio = new Carbon($period[$i - 1]);
                $requestjson->fechaFin = new Carbon($period[$i]);
                $rango = $this->singleData($requestjson);
                if ($rango->data) {
                    $rango->fecha_inicial = $requestjson->fechaInicio->format('Y-m-d H:i');
                    $rango->fecha_final = $requestjson->fechaFin->add(-1, 'day')->format('Y-m-d H:i');
                    array_push($response->data, $rango);
                }
            }
//            $response->data = [];
//            $period = CarbonPeriod::create($requestjson->fechaInicio, $requestjson->frecuencia, $requestjson->fechaFin);
//            $period = $period->toArray();
//            for($i = 1; $i < count($period); $i++){
//                if ($requestjson->tipoResultado === "Periódico") $requestjson->fechaInicio = new Carbon($period[$i - 1]);
//                $requestjson->fechaFin = new Carbon($period[$i]);
//                $rango = $this->singleData($requestjson);
//                if ($rango->data) {
//                    $rango->fecha_inicial = $requestjson->fechaInicio->format('Y-m-d');
//                    $rango->fecha_final = $requestjson->fechaFin->add(-1, 'day')->format('Y-m-d');
//                    array_push($response->data, $rango);
//                }
//            }
//            $response->request = $requestjson;
//            return response()->json($response, 200);
        }


        $response->request = $requestjson;
        return response()->json($response, 200);
    }

    public function singleData ($requestjson) {
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
            case 'Planta': {
                $response->planta = Planta::where('id', '=', $requestjson->taxonomia_id)->first();
                $response->data = $this->disponibilidadRangoPlanta($requestjson);
                break;
            }
        }
        return $response;
    }

    public function disponibilidadRangoPlanta ($request) {
        $requestjson = clone $request;
        $sistemas = Sistema::where('planta_id', '=', $requestjson->taxonomia_id)->get();
        if ($sistemas->count()) {
            $objeto =  new disponibilidad();
            $minutos_falla = 0;
            $minutos_reparacion = 0;
            $minutos_intervalo = 0;
            foreach ($sistemas as $sistema) {
                $response_sistema = new stdClass();
                $response_sistema->sistema = Sistema::where('id', '=', $sistema->id)->first();
                $requestjson->taxonomia_id = $sistema->id;
                $response_sistema->data = $this->disponibilidadRangoSistema($requestjson);
                if ($response_sistema->data) {
                    $minutos_falla = $minutos_falla + ($response_sistema->data->falla ? $response_sistema->data->falla->total_minutos : 0);
                    $minutos_reparacion = $minutos_reparacion + ($response_sistema->data->reparacion ? $response_sistema->data->reparacion->total_minutos : 0);
                    $objeto->fallas = $objeto->fallas + $response_sistema->data->fallas;
                    $objeto->reparaciones = $objeto->reparaciones + $response_sistema->data->reparaciones;
                    $minutos_intervalo = $response_sistema->data->intervalo->total_minutos;
                    array_push($objeto->registros, $response_sistema);
                }
            }
            $objeto->intervalo = $this->objectTime($minutos_intervalo * $sistemas->count());
            $objeto->falla = $this->objectTime($minutos_falla);
            $objeto->reparacion = $this->objectTime($minutos_reparacion);

            //Tiempo medio entre fallas
            $objeto->mtbf = $this->calculaMtbf(($objeto->intervalo->total_minutos - $objeto->falla->total_minutos), $objeto->fallas);

            //Tiempo medio entre reparaciones
            $objeto->mttr = $this->calculaMttr($objeto->falla->total_minutos, $objeto->fallas);

            //Disponibilidad
            $objeto->disponibilidad = $requestjson->typeKpi === 'Disponibilidad' ? $this->calculaDisponibilidad($objeto->intervalo->total_minutos, $objeto->falla->total_minutos) : $this->calculaConfiabilidad($objeto->mtbf->total_minutos, $requestjson->tiempoMision);
            return $objeto;
        } else {
            return null;
        }
    }

    public function disponibilidadRangoSistema ($request) {
        $requestjson = clone $request;
        $equipos = Equipo::where('sistema_id', '=', $requestjson->taxonomia_id)->get();
        if ($equipos->count()) {
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
            $objeto->intervalo = $this->objectTime($minutos_intervalo * $equipos->count());
            $objeto->falla = $this->objectTime($minutos_falla);
//        dd($objeto->falla);
            $objeto->reparacion = $this->objectTime($minutos_reparacion);

            //Tiempo medio entre fallas
            $objeto->mtbf = $this->calculaMtbf(($objeto->intervalo->total_minutos - $objeto->falla->total_minutos), $objeto->fallas);

            //Tiempo medio entre reparaciones
            $objeto->mttr = $this->calculaMttr($objeto->falla->total_minutos, $objeto->fallas);

            //Disponibilidad
            $objeto->disponibilidad = $requestjson->typeKpi === 'Disponibilidad' ? $this->calculaDisponibilidad($objeto->intervalo->total_minutos, $objeto->falla->total_minutos) : $this->calculaConfiabilidad($objeto->mtbf->total_minutos, $requestjson->tiempoMision);
            return $objeto;
        } else {
            return null;
        }
    }

    public function disponibilidadRangoEquipo ($requestjson) {
        $objeto =  new disponibilidad();
        $eventos = Evento::where(function($query) use ($requestjson) {
            if ($requestjson->contractual) {
                $query->where('contractual', '=', 1);
            }
        })->where('equipo_id', '=', $requestjson->taxonomia_id)
            ->whereIn('tipo_evento_id', $requestjson->tipoEvento)->get();
        //tiempo total intervalo
        $objeto->intervalo = $this->objectTime($requestjson->fechaFin->diffInMinutes($requestjson->fechaInicio));
        if ($eventos->count()) {
            $total_minutos_falla_equipo = 0;
//            $total_minutos_reparacion_equipo = 0;
            foreach ($eventos as $evento) {
                if($this->check_date_range($requestjson->fechaInicio, $requestjson->fechaFin, $evento->fecha_inicio, $evento->fecha_fin)) {
                    //fechas
                    $copia_fecha_inicio = $evento->fecha_inicio ? new Carbon($evento->fecha_inicio) : $requestjson->fechaInicio;
                    $copia_fecha_fin = $evento->fecha_fin ? new Carbon($evento->fecha_fin) : new Carbon();
                    $diferencia_fecha_inicio = $requestjson->fechaInicio->diffInMinutes($copia_fecha_inicio, false);
                    $diferencia_fecha_fin = $requestjson->fechaFin->diffInMinutes($copia_fecha_fin, false);

                    $calculo_fecha_inicio = (($diferencia_fecha_inicio === 0) ? $requestjson->fechaInicio : ($diferencia_fecha_inicio < 0) && ($requestjson->fechaInicio->diffInMinutes($copia_fecha_fin, false))) ? $requestjson->fechaInicio : $copia_fecha_inicio;
                    $calculo_fecha_fin = ($diferencia_fecha_fin === 0) ? $requestjson->fechaFin : ($diferencia_fecha_fin < 0) ? $copia_fecha_fin : $requestjson->fechaFin;

                    if (($calculo_fecha_inicio->diffInMinutes($calculo_fecha_fin, false) !== 0) && ($calculo_fecha_inicio->diffInMinutes($requestjson->fechaInicio, false) <= 0)) {
                        $objeto->fallas++;
                        $thefalla = $this->objectTime($calculo_fecha_inicio->diffInMinutes($calculo_fecha_fin));
                        $total_minutos_falla_equipo = $total_minutos_falla_equipo + $thefalla->total_minutos;
                    }

                    // lo viejo
//                    $objeto->fallas++;
                    //fallando
//                    $copia_fecha_inicio = $evento->fecha_inicio ? new Carbon($evento->fecha_inicio) : $requestjson->fechaInicio;
//                    $copia_fecha_fin = $evento->fecha_fin ? new Carbon($evento->fecha_fin) : new Carbon();
//                    $calculo_fecha_inicio = ($requestjson->fechaInicio->diffInMinutes($copia_fecha_inicio, false) >= 0) ? $copia_fecha_inicio : $requestjson->fechaInicio;
//                    $calculo_fecha_final = ($requestjson->fechaFin->diffInMinutes($copia_fecha_fin, false) <= 0) ? $copia_fecha_fin : $requestjson->fechaFin;

//                    $thefalla = $this->objectTime($calculo_fecha_inicio->diffInMinutes($calculo_fecha_fin));
//                    $evento->falla = $this->objectTime($calculo_fecha_inicio->diffInMinutes($calculo_fecha_final));
//                    $evento->fecha_inicio = $calculo_fecha_inicio;
//                    $evento->fecha_fin = $calculo_fecha_final;
//                    $evento->downtime = $evento->falla->total_horas;
//                    $total_minutos_falla_equipo = $total_minutos_falla_equipo + $thefalla->total_minutos;
//                    //reparación
//                    $evento->reparacion = $this->objectTime(0);
//                    if($this->check_date_range($requestjson->fechaInicio, $requestjson->fechaFin, $evento->fecha_inicio_reparacion, $evento->fecha_fin_reparacion)) {
//                        $objeto->reparaciones++;
//                        $copia_fecha_inicio = $evento->fecha_inicio_reparacion ? new Carbon($evento->fecha_inicio_reparacion) : $requestjson->fechaInicio;
//                        $copia_fecha_fin = $evento->fecha_fin_reparacion ? new Carbon($evento->fecha_fin_reparacion) : new Carbon();
//                        $calculo_fecha_inicio = ($requestjson->fechaInicio->diffInMinutes($copia_fecha_inicio, false) >= 0) ? $copia_fecha_inicio : $requestjson->fechaInicio;
//                        $calculo_fecha_final = ($requestjson->fechaFin->diffInMinutes($copia_fecha_fin, false) <= 0) ? $copia_fecha_fin : $requestjson->fechaFin;
//
//                        $evento->reparacion = $this->objectTime($calculo_fecha_inicio->diffInMinutes($calculo_fecha_final));
//                        $total_minutos_reparacion_equipo = $total_minutos_reparacion_equipo + $evento->reparacion->total_minutos;
//                    }
//                    array_push($objeto->registros, $evento);
                }
            }
            if ($objeto->fallas) {
                //Tiempo medio entre fallas
                $objeto->mtbf = $this->calculaMtbf(($objeto->intervalo->total_minutos - $total_minutos_falla_equipo), $objeto->fallas);

                //tiempo total fallas
                $objeto->falla = $this->objectTime($total_minutos_falla_equipo);

                //Tiempo medio entre reparaciones
                $objeto->mttr = $this->calculaMttr($total_minutos_falla_equipo, $objeto->fallas);

                //Disponibilidad
                $objeto->disponibilidad = $requestjson->typeKpi === 'Disponibilidad' ? $this->calculaDisponibilidad($objeto->intervalo->total_minutos, $total_minutos_falla_equipo) : $this->calculaConfiabilidad($objeto->mtbf->total_minutos, $requestjson->tiempoMision);


//                //tiempo total reparaciones
//                $objeto->reparacion = $this->objectTime($total_minutos_reparacion_equipo);
            }
        }
        return $objeto;
    }

    public function calculaMttr ($minutos_falla, $cantidad_fallas = 1) {
        if ($cantidad_fallas === 0) $cantidad_fallas = 1;
        return $this->objectTime(round(($minutos_falla / $cantidad_fallas), 2));
    }

    public function calculaMtbf ($minutos_operacion, $cantidad_fallas = 1) {
        if ($cantidad_fallas === 0) $cantidad_fallas = 1;
        return $this->objectTime(round(($minutos_operacion / $cantidad_fallas), 2));
    }

    public function calculaDisponibilidad ($minutos_intervalo, $minutos_falla) {
        if (($minutos_intervalo - $minutos_falla) === 0) return '0%';
        return round(((($minutos_intervalo - $minutos_falla) / $minutos_intervalo) * 100), 2).'%';
    }

    public function calculaConfiabilidad ($mtbf_minutos, $tiempo_mision) {
        if ($mtbf_minutos === 0) return '0%';
        return round(((exp(-((1/($mtbf_minutos/60)) * $tiempo_mision))) * 100), 2).'%';
    }
}
