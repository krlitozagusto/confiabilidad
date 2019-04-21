<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Falla extends Model
{
    public function modo_falla()
    {
        return $this->belongsTo(ModoFalla::class);
    }

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    protected $hidden = ['created_at','updated_at'];
}
