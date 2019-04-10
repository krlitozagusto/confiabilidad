<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoGasto extends Model
{
    public function gastos()
    {
        return $this->hasMany(Gasto::class);
    }

    protected $hidden = ['created_at','updated_at'];
}
