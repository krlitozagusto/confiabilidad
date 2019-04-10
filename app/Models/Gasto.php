<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    public function tipo_gasto()
    {
        return $this->belongsTo(TipoGasto::class);
    }

    protected $hidden = ['created_at','updated_at'];
}
