<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Planta extends Model
{
    public function sistemas()
    {
        return $this->hasMany(Sistema::class);
    }

    public function campo()
    {
        return $this->belongsTo(Campo::class);
    }

    protected $hidden = ['created_at','updated_at'];

    public function scopeSearch(Builder $builder,$search) : Builder
    {
        return $builder->where(function($query) use($search){
            $query
                ->orWhereHas('campo',function ($query) use ($search) {
                    $query->where('nombre','like','%'.$search.'%')
                        ->orWhere('codigo','like','%'.$search.'%');
                });
        })->orWhere(function($query) use ($search){
            $query->where('nombre','like','%'.$search.'%')
                ->orWhere('descripcion','like','%'.$search.'%')
                ->orWhere('emplazamiento','like','%'.$search.'%');
        });
    }
}
