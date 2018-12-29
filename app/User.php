<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','is_admin'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function completed_modules()
    {
        return $this->belongsToMany('App\Module', 'user_completed_modules');
    }

    public function scopeContacts($query) 
    {
        return $query->where('is_admin', 0);
    }

    public function isAdmin() 
    {
        return $this->is_admin === 1;
    }
}
