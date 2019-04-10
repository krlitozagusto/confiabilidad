<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImagenEquipo extends Model
{
    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    protected $hidden = ['created_at','updated_at'];
}
