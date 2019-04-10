<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    public function empleados()
    {
        return $this->hasMany(Empleado::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    protected $hidden = ['created_at','updated_at'];
}
