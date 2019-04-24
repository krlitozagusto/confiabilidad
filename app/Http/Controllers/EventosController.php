<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\TipoEvento;
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
            'tiposMantenimiento'=> TipoMantenimiento::all()
        ]);
    }

    public function editEvent(Request $request)
    {
        return response()->json([
            'evento' => Evento::where('id','=',$request->id)->with('evento_padre', 'eventos_hijos', 'equipo')->first(),
            'tiposEvento'=> TipoEvento::all(),
            'tiposMantenimiento'=> TipoMantenimiento::all()
        ]);
    }

    public function registerEvent(Request $request)
    {
        DB::beginTransaction();
        try{
            $validador = Validator ::make($request->all(),[
                'fecha_registro'=>'required|date',
                'fecha_inicio'=>'required|date',
                'fecha_fin'=>'date',
                'fecha_inicio_reparacion'=>'date',
                'fecha_fin_reparacion'=>'date',
                'estado'=>'required|string',
                'contractual'=>'required|boolean',
                'programado'=>'required|boolean',
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
            $evento = $request['id'] ? Evento::where('id','=',$request['id'])->first() : new Evento();
            $evento->fill($request->all());
            $evento->downtime = 0;
            $evento->user_id = Auth::id();
            $evento->save();
            DB::commit();
            return response()->json([
                'evento' => Evento::where('id','=',$evento->id)->with('tipo_evento', 'equipo')->first()
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
