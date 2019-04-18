<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    public function imagen_equipos()
    {
        return $this->hasMany(ImagenEquipo::class);
    }

    public function eventos()
    {
        return $this->hasMany(Evento::class);
    }

    public function ubicacion_tecnica()
    {
        return $this->belongsTo(UbicacionTecnica::class);
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

    public function numero_equipo()
    {
        return $this->belongsTo(NumeroEquipo::class);
    }

    public function valoracion_ram()
    {
        return $this->belongsTo(ValoracionRam::class);
    }

    public function plan_mantenimiento()
    {
        return $this->belongsTo(PlanMantenimiento::class);
    }

    public function centro_costo()
    {
        return $this->belongsTo(CentroCosto::class);
    }

    protected $hidden = ['created_at','updated_at'];
}
