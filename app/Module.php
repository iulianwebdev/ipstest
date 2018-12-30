<?php

namespace App;

use App\Collections\ModuleCollection;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{

    /**
     * Return a custom Collection class when Model does instantiation
     * @param  array  $modules<Module>
     *
     * @return ModuleCollection
     */
    public function newCollection(array $modules = [])
    {
        return new ModuleCollection($modules);
    }

    public function scopeAvailable($query)
    {
        return $query->select('course_key')->distinct()->get()->pluck('course_key')->toArray();
    }

    /**
     * Returns the Module instance that is last in course
     * will rely on ids to be relative to the order
     * 
     * @param  Builder $query 
     * @return Module
     */
    public function scopeWithLastLabel($query)
    {
        return $query->where('course_key', $this->course_key)->orderBy('id', 'desc')->limit(1)->get()->first();
    }
}
