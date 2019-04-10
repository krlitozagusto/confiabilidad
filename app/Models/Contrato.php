<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    public function campos()
    {
        return $this->hasMany(Campo::class);
    }

    protected $hidden = ['created_at','updated_at'];
}
