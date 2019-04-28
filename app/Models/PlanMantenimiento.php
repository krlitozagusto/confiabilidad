<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanMantenimiento extends Model
{
    public function equipos()
    {
        return $this->belongsTo(Equipo::class);
    }

    protected $hidden = ['created_at','updated_at'];
}
