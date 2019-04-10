<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenTrabajo extends Model
{
    public function puesto_trabajo()
    {
        return $this->belongsTo(PuestoTrabajo::class);
    }

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    protected $hidden = ['created_at','updated_at'];
}
