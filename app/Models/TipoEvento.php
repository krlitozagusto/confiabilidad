<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoEvento extends Model
{
    public function eventos()
    {
        return $this->hasMany(Evento::class);
    }

    protected $hidden = ['created_at','updated_at'];
}
