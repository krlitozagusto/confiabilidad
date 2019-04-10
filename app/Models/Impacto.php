<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Impacto extends Model
{
    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    public function tipo_impacto()
    {
        return $this->belongsTo(TipoImpacto::class);
    }

    protected $hidden = ['created_at','updated_at'];
}
