<?php

namespace App\Http\Controllers;

use App\Models\Campo;
use App\Models\Comentario;
use App\Models\Evento;
use App\Models\Falla;
use App\Models\Gasto;
use App\Models\Impacto;
use App\Models\ModoFalla;
use App\Models\OrdenTrabajo;
use App\Models\PuestoTrabajo;
use App\Models\TipoEvento;
use App\Models\TipoGasto;
use App\Models\TipoImpacto;
use App\Models\TipoMantenimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Input;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class EventosController extends Controller
{
    public function panel()
    {
        $perPage = Input::get('per_page');

        $query = QueryBuilder::for(Evento::class)
            ->with('tipo_evento', 'equipo.sistema.planta.campo')
            ->allowedFilters([
                Filter::scope('search')
            ])
            ->allowedSorts('id', 'evento_padre_id', 'estado');

        if($perPage){
            return new Resource($query->paginate($perPage));
        }
        return new Resource($query->get());
    }

    public function newEvent()
    {
        return response()->json([
            'evento'=> null,
            'tiposEvento'=> TipoEvento::all(),
            'tiposMantenimiento'=> TipoMantenimiento::all(),
            'puestosTrabajo'=> PuestoTrabajo::all(),
            'modosFalla'=> ModoFalla::all(),
            'tiposImpacto'=> TipoImpacto::all(),
            'tiposGasto'=> TipoGasto::all()
        ]);
    }

    public function getEvent(Request $request)
    {
        return response()->json([
            'evento' => Evento::where('id','=',$request->id)->with([
                'evento_padre.equipo',
                'evento_padre.tipo_evento',
                'tipo_evento',
                'tipo_mantenimiento',
                'eventos_hijos.equipo',
                'eventos_hijos.tipo_evento',
                'equipo.sistema.planta.campo',
                'comentarios.usuario',
                'orden_trabajos.puesto_trabajo',
                'fallas.modo_falla',
                'impactos.tipo_impacto',
                'gastos.tipo_gasto'
            ])->first()
        ]);
    }

    public function editEvent(Request $request)
    {
        return response()->json([
            'evento' => Evento::where('id','=',$request->id)->with([
                'evento_padre.equipo',
                'evento_padre.tipo_evento',
                'eventos_hijos',
                'equipo.sistema.planta.campo',
                'orden_trabajos',
                'fallas.modo_falla',
                'impactos.tipo_impacto',
                'gastos.tipo_gasto'
            ])->first(),
            'tiposEvento'=> TipoEvento::all(),
            'tiposMantenimiento'=> TipoMantenimiento::all(),
            'puestosTrabajo'=> PuestoTrabajo::all(),
            'puestosTrabajo'=> PuestoTrabajo::all(),
            'modosFalla'=> ModoFalla::all(),
            'tiposImpacto'=> TipoImpacto::all(),
            'tiposGasto'=> TipoGasto::all()
        ]);
    }

    public function registerEvent(Request $request)
    {
        DB::beginTransaction();
        try{
            $validador = Validator ::make($request->all(),[
                'fecha_registro'=>'required|date',
                'fecha_inicio'=>'required|date',
                'estado'=>'required|string',
                'contractual'=>'required|boolean',
                'tipo_evento_id'=>'required|integer',
                'tipo_mantenimiento_id'=>'required|integer',
                'equipo_id'=>'required|integer'
            ]);
            if($validador->fails()){
                DB::rollback();
                return response()->json([
                    'error'=> $validador->errors()->first()
                ], 422);
            }
            $requestjson = json_decode($request->getContent());
            $evento = $requestjson->id ? Evento::where('id','=',$requestjson->id)->first() : new Evento();
            $evento->fill($request->all());
            $evento->downtime = 0;
            $evento->user_id = Auth::id();
            $evento->save();
            
            //Las ordenes de trabajo
            $ordenes = OrdenTrabajo::where('evento_id', '=', $evento->id)->get();
            if ($ordenes->count()) {
                foreach ($ordenes as $orden) {
                    if (!$orden->delete()) {
                        DB::rollback();
                        return response()->json([
                            'error'=> 'La orden no se pudo editar.'
                        ], 422);
                    }
                }
            }
            foreach ($requestjson->orden_trabajos as $item) {
                $orden_trabajo = new OrdenTrabajo();
                $orden_trabajo->evento_id = $evento->id;
                $orden_trabajo->numero_orden = $item->numero_orden;
                $orden_trabajo->numero_aviso = $item->numero_aviso;
                $orden_trabajo->descripcion = $item->descripcion;
                $orden_trabajo->puesto_trabajo_id = $item->puesto_trabajo_id;
                $orden_trabajo->save();
            }

            //Las fallas
            $fallas_old = Falla::where('evento_id', '=', $evento->id)->get();
            if ($fallas_old->count()) {
                foreach ($fallas_old as $falla_old) {
                    if (!$falla_old->delete()) {
                        DB::rollback();
                        return response()->json([
                            'error'=> 'Las fallas no se pudieron actualizar.'
                        ], 422);
                    }
                }
            }
            foreach ($requestjson->fallas as $item) {
                $falla = new Falla();
                $falla->evento_id = $evento->id;
                $falla->sintoma = $item->sintoma;
                $falla->sistema = $item->sistema;
                $falla->parte = $item->parte;
                $falla->accion_correctiva = $item->accion_correctiva;
                $falla->modo_falla_id = $item->modo_falla_id;
                $falla->save();
            }

            //Los Impactos
            $impactos_old = Impacto::where('evento_id', '=', $evento->id)->get();
            if ($impactos_old->count()) {
                foreach ($impactos_old as $impacto_old) {
                    if (!$impacto_old->delete()) {
                        DB::rollback();
                        return response()->json([
                            'error'=> 'Los impactos no se pudieron actualizar.'
                        ], 422);
                    }
                }
            }
            foreach ($requestjson->impactos as $item) {
                $impacto = new Impacto();
                $impacto->evento_id = $evento->id;
                $impacto->cantidad = $item->cantidad;
                $impacto->tipo_impacto_id = $item->tipo_impacto_id;
                $impacto->save();
            }

            //Los Gastos
            $gastos_old = Gasto::where('evento_id', '=', $evento->id)->get();
            if ($gastos_old->count()) {
                foreach ($gastos_old as $gasto_old) {
                    if (!$gasto_old->delete()) {
                        DB::rollback();
                        return response()->json([
                            'error'=> 'Los gastos no se pudieron actualizar.'
                        ], 422);
                    }
                }
            }
            foreach ($requestjson->gastos as $item) {
                $gasto = new Gasto();
                $gasto->evento_id = $evento->id;
                $gasto->cantidad = $item->cantidad;
                $gasto->valor = $item->valor;
                $gasto->observaciones = $item->observaciones;
                $gasto->tipo_gasto_id = $item->tipo_gasto_id;
                $gasto->save();
            }
            DB::commit();
            return response()->json([true], 200);
        }catch (\Exception $exception) {
            DB::rollback();
            return response()->json([
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function registerComment(Request $request)
    {
        DB::beginTransaction();
        try{
            $validador = Validator ::make($request->all(),[
                'evento_id'=>'required',
                'descripcion'=>'required'
            ]);
            if($validador->fails()){
                DB::rollback();
                return response()->json([
                    'error'=> $validador->errors()->first()
                ], 422);
            }
            $comentario = new Comentario();
            $comentario->evento_id = $request['evento_id'];
            $comentario->descripcion = $request['descripcion'];
            $comentario->descripcion = $request['descripcion'];
            $comentario->fecha = new \DateTime();
            $comentario->user_id = Auth::id();
            $comentario->save();
            DB::commit();
            return response()->json([
                'comentario' => Comentario::where('id','=',$comentario->id)->with('usuario')->first()
            ], 200);
        }catch (\Exception $exception) {
            DB::rollback();
            return response()->json([
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function loadFile(Request $request) {
        DB::beginTransaction();
        try {
            $file = $request->file('archivo_soporte');
            $nombre = $file->getClientOriginalName();
            $evento = Evento::where('id','=',$request->evento_id)->first();
            if ($evento->archivo_soporte) {
                Storage::delete("Eventos/{$evento->id}/Soportes/".$evento->archivo_soporte);
            }
            $evento->archivo_soporte = $nombre;
            \Storage::disk('local')->put("Eventos/{$evento->id}/Soportes/".$nombre, \File::get($file));
            $evento->save();
            DB::commit();
            return response()->json([
                'archivo' => $nombre
            ], 200);
        }catch (\Exception $exception) {
            DB::rollback();
            return response()->json([
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function downloadSoporte($id) {
        try {
            $evento = Evento::where('id','=',$id)->first();
//            $public_path = "/home/bdsystem/public_html/storage";
            $public_path = "C:\laragon\www\confiabilidad\storage";
            $url = $public_path."/app/Eventos/{$evento->id}/Soportes/{$evento->archivo_soporte}";
            $headers = array(
                'Content-Type: application/pdf',
            );
            return response()->download($url, $evento->archivo_soporte, $headers);
        }catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function postulador()
    {
        $query = QueryBuilder::for(Evento::class)
            ->with('tipo_evento', 'equipo')
            ->where('evento_padre_id', '=', null)
            ->allowedFilters([
                Filter::exact('estado'),
                Filter::scope('search')
            ]);
        return new Resource($query->get());
    }

    public function cancelEvent(Request $request)
    {
        DB::beginTransaction();
        try{
            $evento = Evento::where('id','=',$request->id)->first();
            if($evento->estado === 'Terminado'){
                DB::rollback();
                return response()->json([
                    'error'=> 'El evento está terminado y no es posible anularlo.'
                ], 422);
            } else {
                $evento->estado = 'Anulado';
                $evento->save();
                $secundarios = Evento::where('evento_padre_id','=',$evento->id)->get();
                if ($secundarios->count()) {
                    foreach ($secundarios as $secundario) {
                        if ($secundario->estado === 'Terminado') {
                            DB::rollback();
                            return response()->json([
                                'error'=> 'Uno de los eventos secundarios está terminado y no puede ser anulado.'
                            ], 422);
                        } else {
                            $secundario->estado = 'Anulado';
                            $secundario->save();
                        }
                    }
                }
            }
            DB::commit();
            return response()->json([], 200);
        }catch (\Exception $exception) {
            DB::rollback();
            return response()->json([
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function dianmicList(Request $request)
    {
        $requestjson = json_decode($request->getContent());
//        dd($requestjson);
        switch ($requestjson->tipoTaxonomia) {
            case 'Equipo': {
                $query = QueryBuilder::for(Evento::class)
                    ->with('tipo_evento', 'equipo.sistema.planta.campo.contrato')
                    ->where('equipo_id', '=', $requestjson->taxonomia_id);
                break;
            }
            case 'Sistema': {
                break;
            }
            case 'Planta': {
                break;
            }
        }
        return new Resource($query->get());
    }
}
