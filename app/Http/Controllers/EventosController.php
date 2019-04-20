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

    public function newEvento()
    {
        return response()->json([
            'evento'=> null,
            'tiposEvento'=> TipoEvento::all(),
            'tiposMantenimiento'=> TipoMantenimiento::all()

        ]);
    }

    public function registerNewEvent(Request $request)
    {
        DB::beginTransaction();
        try{
            $validador = Validator ::make($request->all(),[
                'name' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'empleado' => 'required'
            ]);
            if($validador->fails()){
                DB::rollback();
                return response()->json([
                    'error'=> $validador->errors()->first()
                ], 422);
            }
            $usuario = new User();
            $usuario->fill($request->all());
            $usuario->password = Hash::make('Confiabilidad'.$request['empleado']['identificacion']);
            $usuario->avatar = 'avatarDefault.png';
            $usuario->save();
            $empleado = Empleado::where('identificacion','=',$request['empleado']['identificacion'])->first();
            $empleado->user_id = $usuario->id;
            $empleado->save();
            DB::commit();
            return response()->json([
                'usuario' => User::where('id','=',$usuario->id)->with('empleado')->first()
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
            ->with('tipo_evento', 'equipo.tag')
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
