<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'description'
    ];

    public function tags() 
    {
        return $this->hasMany('App\Tag');
    }
    
}
