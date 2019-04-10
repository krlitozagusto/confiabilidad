<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValoracionRam extends Model
{
    public function equipos()
    {
        return $this->hasMany(Equipo::class);
    }

    protected $hidden = ['created_at','updated_at'];
}
