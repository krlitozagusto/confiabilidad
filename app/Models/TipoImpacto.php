<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoImpacto extends Model
{
    public function impactos()
    {
        return $this->hasMany(Impacto::class);
    }

    protected $hidden = ['created_at','updated_at'];
}
