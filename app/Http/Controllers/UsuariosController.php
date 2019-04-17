<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Input;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class UsuariosController extends Controller
{

    public function index()
    {
        $perPage = Input::get('per_page');

        $query = QueryBuilder::for(User::class)
            ->allowedFilters([
                Filter::scope('search'),
                'name',
                'email'
            ]);

        if($perPage){
            return new Resource($query->paginate($perPage));
        }
        dd($query->get());
        return new Resource($query->get());
    }

    public function panel()
    {
//        return response()->json([
//            'usuarios'=> User::with('empleado')->get()
//        ]);
        $perPage = Input::get('per_page');

        $query = QueryBuilder::for(User::class)
            ->with('empleado')
            ->allowedFilters([
                Filter::scope('search'),
                'name',
                'email'
            ]);

        if($perPage){
            return new Resource($query->paginate($perPage));
        }
        return new Resource($query->get());
    }

    public function newUser()
    {
        return response()->json([
            'usuario'=> null,
            'roles'=> Role::all(),
            'empleados'=> Empleado::where([['user_id', '=', null],['estado', '=', 'Activo']])->get(),

        ]);
    }

    public function currentUser()
    {
        return response()->json([
            'currentUser'=> Auth::user()
        ]);
    }

    public function registerNewUser(Request $request)
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

    public function changePassword(Request $request)
    {
        DB::beginTransaction();
        try{
            $validador = Validator ::make($request->all(),[
                'current_password' => 'required',
                'password' => 'required|min:6|same:password',
                'password_confirmation' => 'required|min:6|same:password'
            ]);
            if($validador->fails()){
                DB::rollback();
                return response()->json([
                    'error'=> $validador->errors()->first()
                ], 422);
            }
            if(Hash::check($request->current_password, Auth::user()->password)){
                $user = Auth::user();
                $user->password = bcrypt($request->password);
                $user->save();
                DB::commit();
                return response()->json([
                    'usuario' => User::where('id','=',$user->id)->first()
                ], 200);
            }else{
                DB::rollback();
                return response()->json([
                    'error' => 'La contraseña actual ingresada no coincide con la contraseña actual del usuario',
                ], 500);
            }
        }catch (\Exception $exception) {
            DB::rollback();
            return response()->json([
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        DB::beginTransaction();
        try{
            $usuario = User::where('id','=',$request->id)->with('empleado')->first();
            if ($usuario->empleado) {
                $usuario->password = Hash::make('Confiabilidad'.$usuario->empleado->identificacion);
            } else {
                $usuario->password = Hash::make('Confiabilidad');
            }
            $usuario->save();
            DB::commit();
            return response()->json([], 200);
        }catch (\Exception $exception) {
            DB::rollback();
            return response()->json([
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function editarUsuario(Request $request)
    {
        DB::beginTransaction();
        try{
            $validador = Validator ::make($request->all(),[
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
                'email' => Rule::unique('users')->ignore($request->id)
            ]);
            if($validador->fails()){
                DB::rollback();
                return response()->json([
                    'estado'=>'validator',
                    'error'=> $validador->errors()->first()
                ]);
            }
            $usuario = User::find($request->id);
            $usuario->fill($request->all());
            $usuario->save();
            DB::commit();
            return response()->json([
                'estado'=>'success',
                'usuario' => User::where('id','=',$usuario->id)->first()
            ]);
        }catch (\Exception $exception) {
            DB::rollback();
            return response()->json([
                'error' => $exception->getMessage(),
            ]);
        }
    }
    public function validarEmail(Request $request)
    {
        try{
            $response = false;
            $validador = Validator::make($request->all(), [
                'email' => [
                    'required ',
                    Rule::unique('users')->ignore($request->id)
                ]
            ]);
            if ($validador->fails()) {
                $response = true;
            }else if($response->id){
                $response = false;
            }
            return response()->json([
                'estado' => $response,
            ]);
        }catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
