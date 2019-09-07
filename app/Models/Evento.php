<?php

namespace App\Models;


use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Evento extends Model
{
    public function tipo_evento()
    {
        return $this->belongsTo(TipoEvento::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tipo_mantenimiento()
    {
        return $this->belongsTo(TipoMantenimiento::class);
    }

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class);
    }

    public function fallas()
    {
        return $this->hasMany(Falla::class);
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

    public function eventos_hijos()
    {
        return $this->hasMany(Evento::class, 'evento_padre_id');
    }

    public function evento_padre()
    {
        return $this->belongsTo(Evento::class, 'evento_padre_id');
    }

    protected $hidden = ['created_at','updated_at'];
    protected $fillable = [
        'fecha_registro',
        'fecha_inicio',
        'fecha_fin',
        'fecha_inicio_reparacion',
        'fecha_fin_reparacion',
        'estado',
        'contractual',
        'tipo_evento_id',
        'tipo_mantenimiento_id',
        'equipo_id',
        'evento_padre_id'
    ];

    public function getEquipoIdAttribute($value)
    {
        return (int)$value;

    }

    public function getContractualAttribute($value)
    {
        return (int)$value;

    }

    public function getTipoEventoIdAttribute($value)
    {
        return (int)$value;

    }

    public function getTipoMantenimientoIdAttribute($value)
    {
        return (int)$value;

    }

    public function getUserIdAttribute($value)
    {
        return (int)$value;

    }

    public function scopeSearch(Builder $builder,$search) : Builder
    {
        return $builder->where(function($query) use($search){
            $query->orWhereHas('tipo_evento',function ($query) use ($search) {
                $query->where('nombre','like','%'.$search.'%');
            })->orWhereHas('equipo',function ($query) use ($search) {
                $query->where('nombre','like','%'.$search.'%')
                    ->orWhere('tag','like','%'.$search.'%')
                    ->orWhere('numero_equipo','like','%'.$search.'%')
                    ->orWhereHas('sistema',function ($query) use ($search) {
                        $query->where('nombre','like','%'.$search.'%')
                            ->orWhere('tag','like','%'.$search.'%')
                            ->orWhere('descripcion','like','%'.$search.'%')
                            ->orWhereHas('planta',function ($query) use ($search) {
                                $query->where('nombre','like','%'.$search.'%')
                                    ->orWhere('descripcion','like','%'.$search.'%');
                            });
                    });
            });
        })->orWhere(function($query) use ($search){
            $query->where('id','like','%'.$search.'%')
                ->orWhere('estado','like','%'.$search.'%');
        });
    }
}
