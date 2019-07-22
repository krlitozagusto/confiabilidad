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

    public function sistema()
    {
        return $this->belongsTo(Sistema::class);
    }

    public function valoracion_ram()
    {
        return $this->belongsTo(ValoracionRam::class);
    }

    public function planes_mantenimiento()
    {
        return $this->hasMany(PlanMantenimiento::class);
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
                ->orWhereHas('sistema',function ($query) use ($search) {
                    $query->where('nombre','like','%'.$search.'%')
                        ->orWhere('tag','like','%'.$search.'%');
                });
        })->orWhere(function($query) use ($search){
            $query->orWhere('nombre','like','%'.$search.'%')
                ->orWhere('tag','like','%'.$search.'%')
                ->orWhere('numero_equipo','like','%'.$search.'%');
        });
    }
}
