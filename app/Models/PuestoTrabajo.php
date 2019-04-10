<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PuestoTrabajo extends Model
{
    public function orden_trabajos()
    {
        return $this->hasMany(OrdenTrabajo::class);
    }

    protected $hidden = ['created_at','updated_at'];
}
