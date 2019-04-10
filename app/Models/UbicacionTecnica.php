<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UbicacionTecnica extends Model
{
    public function equipos()
    {
        return $this->hasMany(Equipo::class);
    }

    public function sistema()
    {
        return $this->belongsTo(Sistema::class);
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

    public function numero_equipo()
    {
        return $this->belongsTo(NumeroEquipo::class);
    }

    protected $hidden = ['created_at','updated_at'];
}
