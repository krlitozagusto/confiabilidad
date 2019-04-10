<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    public function cargos()
    {
        return $this->hasMany(Cargo::class);
    }

    protected $hidden = ['created_at','updated_at'];
}
