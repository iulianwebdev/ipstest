<?php 

namespace App\Entities;

use App\Tag;

class Contact {
    
    protected $id;
    protected $groups = [];
    protected $products = [];

    protected $original = [];

    public function __construct(array $data) 
    {
        if (!isset($data['Id'])) {
            throw new \Exception('Contact missing id.');
        }

        $this->id = $data['Id'];

        if (isset($data['_Products']) && !empty($data['_Products'])) {
            $this->products += explode(',', $data['_Products']);
        }  

        if (isset($data['Groups'])) {
            $this->groups = explode(',', $data['Groups']);
        }

        $this->original = $data;
    }

    public function hasCompletedAllModulesTag() 
    {
        $completedTag = Tag::completed();
        return in_array($completedTag->id, $this->groups);
    }

    public function firstModuleKey() 
    {
        return $this->products[0] ?? false;
    }

    public function hasNoCoursesAssigned() 
    {
        return empty($this->products);
    }

    public function __get($key) 
    {
        return $this->{$key}; 
    }
}
