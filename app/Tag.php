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
        'category_id',
    ];

    public function category() 
    {
        return $this->belongsTo('App\Category');
    }

    public function scopeForModule($query, Module $module) 
    {
        return $query->where('name', 'Start '.$module->name.' Reminders')->first();
    }

    public function scopeCompleted($query) 
    {
        return $query->where('name', 'Module reminders completed')->first();
    }
}
