<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'description',
    ];

    public function category() 
    {
        return $this->belongsTo('App\Category');
    }
}
