<?php

namespace App;

use App\Module;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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

    public function scopeAvailableModules($query, $courses) 
    {
        $ids = $this->completed_modules()
                ->distinct('id')
                ->pluck('modules.id')
                ->toArray();
        
        $query = Module::whereIn('course_key', $courses);

        if(!empty($ids)) {
            $query->whereNotIn('id', $ids);
        }

        return $query;
    }

    public function isAdmin() 
    {
        return $this->is_admin === 1;
    }
}
