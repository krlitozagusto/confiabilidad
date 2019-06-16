<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Sistema extends Model
{
    public function equipos()
    {
        return $this->hasMany(Equipo::class);
    }

    public function planta()
    {
        return $this->belongsTo(Planta::class);
    }

    protected $hidden = ['created_at','updated_at'];

    public function scopeSearch(Builder $builder,$search) : Builder
    {
        return $builder->where(function($query) use($search){
            $query
                ->orWhereHas('planta',function ($query) use ($search) {
                    $query->where('nombre','like','%'.$search.'%')
                        ->orWhere('emplazamiento','like','%'.$search.'%');
                });
        })->orWhere(function($query) use ($search){
            $query->where('descripcion','like','%'.$search.'%')
                ->orWhere('nombre','like','%'.$search.'%')
                ->orWhere('tag','like','%'.$search.'%');
        });
    }
}
