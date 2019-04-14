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

class UsuariosController extends Controller
{
    public function panel()
    {
        return response()->json([
            'usuarios'=> User::with('empleado')->get()
        ]);
    }

    public function newUser()
    {
        return response()->json([
            'user'=> null,
            'roles'=> Role::all(),
            'empleados'=> Empleado::where('estado', '=', 'Activo')->get(),

        ]);
    }

    public function currentUser()
    {
        return response()->json([
            'currentUser'=> Auth::user()
        ]);
    }

    public function crearUsuario(Request $request)
    {
        DB::beginTransaction();
        try{
            $validador = Validator ::make($request->all(),[
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
            ]);
            if($validador->fails()){
                DB::rollback();
                return response()->json([
                    'estado'=>'validator',
                    'error'=> $validador->errors()->first()
                ]);
            }
            $usuario = new User();
            $usuario->fill($request->all());
            $usuario->password = Hash::make('Sanmia12345');
            $usuario->save();
            DB::commit();
            return response()->json([
                'usuario' => User::where('id','=',$usuario->id)->first()
            ]);
        }catch (\Exception $exception) {
            DB::rollback();
            return response()->json([
                'error' => $exception->getMessage(),
            ]);
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

    public function resetPassword(Request $request)
    {
        DB::beginTransaction();
        try{
            $usuario = User::find($request->id);
            $usuario->password = Hash::make('Sanmia12345');
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
