<?php
Auth::routes();
Route::group(['middleware' => 'auth'], function (){
    Route::get('/eventos/downloadsoporte/{id}', 'EventosController@downloadSoporte');
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
    Route::post('/eventos/postulador', 'EventosController@postulador');

    Route::post('/eventos/get', 'EventosController@getEvent');
    Route::post('/eventos/newevent', 'EventosController@newEvent');
    Route::post('/eventos/editevent', 'EventosController@editEvent');
    Route::post('/eventos/registerevent', 'EventosController@registerEvent');
    Route::post('/eventos/cancelevent', 'EventosController@cancelEvent');

    Route::post('/eventos/registercomment', 'EventosController@registerComment');
    Route::post('/eventos/loadfile', 'EventosController@loadFile');

    Route::post('/eventos/tipos', 'EventosController@getTiposEvento');


//Equipos
    Route::post('/equipos/panel', 'EquiposController@panel');
    Route::post('/equipos/postulador', 'EquiposController@postulador');

    Route::post('/equipos/get', 'EquiposController@getMachine');

    Route::post('/equipos/getcontratos', 'EquiposController@getContratos');

//Sistemas
    Route::post('/sistemas/postulador', 'SistemasController@postulador');

//Plantas
    Route::post('/plantas/postulador', 'PlantasController@postulador');

//Reportes
    Route::post('/reportes/tiempomedio', 'ReportesController@tiempoMedio');
    Route::post('/reportes/listaeventos', 'ReportesController@listaEventos');
});


