<?php

namespace App;

use App\Models\Empleado;
use App\Models\Role;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
    use Notifiable;

    public function empleado()
    {
        return $this->hasOne(Empleado::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'created_at', 'updated_at', 'email_verified_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime'
    ];

    public function scopeSearch(Builder $builder,$search) : Builder
    {
        return $builder->where(function($query) use($search){
            $query->orWhereHas('empleado',function ($query) use ($search) {
                $query->where('nombre','like','%'.$search.'%')
                    ->orWhere('identificacion','like','%'.$search.'%');
            });
        })->orWhere(function($query) use ($search){
            $query->where('name','like','%'.$search.'%')
                ->orWhere('email','like','%'.$search.'%');
        });
    }
}
