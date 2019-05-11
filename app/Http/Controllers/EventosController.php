<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use App\Models\Evento;
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
            ->with('tipo_evento', 'equipo')
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
                'evento_padre',
                'tipo_evento',
                'tipo_mantenimiento',
                'eventos_hijos',
                'equipo',
                'comentarios.usuario',
                'orden_trabajos.puesto_trabajo',
                'fallas.modo_falla',
                'impactos.tipo_impacto'
            ])->first()
        ]);
    }

    public function editEvent(Request $request)
    {
        return response()->json([
            'evento' => Evento::where('id','=',$request->id)->with([
                'evento_padre',
                'eventos_hijos',
                'equipo',
                'orden_trabajos',
                'fallas',
                'impactos',
                'gastos'
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
            foreach ($requestjson->orden_trabajos as $ot) {
                $orden_trabajo = new OrdenTrabajo();
                $orden_trabajo->evento_id = $evento->id;
                $orden_trabajo->numero_orden = $ot->numero_orden;
                $orden_trabajo->numero_aviso = $ot->numero_aviso;
                $orden_trabajo->descripcion = $ot->descripcion;
                $orden_trabajo->puesto_trabajo_id = $ot->puesto_trabajo_id;
                $orden_trabajo->save();
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
}
