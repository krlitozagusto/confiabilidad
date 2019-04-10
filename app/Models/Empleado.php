<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    public function campo()
    {
        return $this->belongsTo(Campo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class);
    }

    protected $hidden = ['created_at','updated_at'];
}
