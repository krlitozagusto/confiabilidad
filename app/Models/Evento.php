<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    public function tipo_mantenimiento()
    {
        return $this->belongsTo(TipoMantenimiento::class);
    }

    public function modo_fallas()
    {
        return $this->belongsToMany(ModoFalla::class);
    }

    public function orden_trabajos()
    {
        return $this->hasMany(OrdenTrabajo::class);
    }

    public function gastos()
    {
        return $this->hasMany(Gasto::class);
    }

    public function impactos()
    {
        return $this->hasMany(Impacto::class);
    }

    public function evento_hijos()
    {
        return $this->hasMany(Evento::class, 'evento_padre_id');
    }

    public function evento_padre()
    {
        return $this->belongsTo(Evento::class, 'evento_padre_id');
    }

    protected $hidden = ['created_at','updated_at'];
}
