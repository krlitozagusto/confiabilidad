<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sistema extends Model
{
    public function ubicacion_tecnicas()
    {
        return $this->hasMany(UbicacionTecnica::class);
    }

    public function planta()
    {
        return $this->belongsTo(Planta::class);
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

    public function numeroequipo()
    {
        return $this->belongsTo(NumeroEquipo::class);
    }

    protected $hidden = ['created_at','updated_at'];
}
