<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
