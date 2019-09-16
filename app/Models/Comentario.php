<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
