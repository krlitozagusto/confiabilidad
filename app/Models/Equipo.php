<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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

    public function scopeSearch(Builder $builder,$search) : Builder
    {
        return $builder->where(function($query) use($search){
            $query
                ->orWhereHas('ubicacion_tecnica',function ($query) use ($search) {
                    $query->where('nombre','like','%'.$search.'%')
                        ->orWhere('tag','like','%'.$search.'%')
                        ->orWhere('numero_equipo','like','%'.$search.'%');
                });
        })->orWhere(function($query) use ($search){
            $query->where('descripcion','like','%'.$search.'%')
                ->orWhere('nombre','like','%'.$search.'%')
                ->orWhere('tag','like','%'.$search.'%')
                ->orWhere('numero_equipo','like','%'.$search.'%');
        });
    }
}
