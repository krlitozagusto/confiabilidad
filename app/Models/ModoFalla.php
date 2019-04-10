<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModoFalla extends Model
{
    public function eventos()
    {
        return $this->belongsToMany(Evento::class);
    }

    protected $hidden = ['created_at','updated_at'];
}
