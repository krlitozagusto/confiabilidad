<?php
Auth::routes();
Route::group(['middleware' => 'auth'], function (){
    Route::get('/{vue_capture?}', function () {
        return view('/home');
    })->where('vue_capture', '[\/\w\.-]*');
//Usuarios
    Route::post('/usuarios/panel', 'UsuariosController@panel');
    Route::post('/usuarios/current', 'UsuariosController@currentUser');
    Route::post('/usuarios/newuser', 'UsuariosController@newUser');
    Route::post('/usuarios/registernewuser', 'UsuariosController@registerNewUser');
    Route::post('/usuarios/changepassword', 'UsuariosController@changePassword');
    Route::post('/usuarios/resetpassword', 'UsuariosController@resetPassword');

//Eventos
    Route::post('/eventos/panel', 'EventosController@panel');
});


