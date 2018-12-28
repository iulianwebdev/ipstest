<?php

use App\Category;
use App\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class TagsSeeder extends Seeder
{
    /**
     * Run the tags table seeder.
     *
     * @return void
     */
    public function run()
    {
        $jsonData = json_decode(Storage::get('tags.json'));

        if(empty($jsonData)){
            throw new \Exception('Couldn\'t retrieve json data.');
        }

        foreach($jsonData as $row){
            $this->createNewTag($row);
        }
    }


    /**
     * Creates one Tag record and inserts a Category if present
     * 
     * @param  stdClass  $tag 
     */
    private function createNewTag(object $tag) 
    {
        if(Tag::find($tag->id)) return;

        $newTag = new Tag;
        
        $newTag->id = $tag->id;
        $newTag->name = $tag->name;
        $newTag->description = $tag->description;

        if(isset($tag->category->id)){

            $category = Category::firstOrCreate([
                'id' => $tag->category->id,
                'name' => $tag->category->name,
                'description' => $tag->category->description,
            ]);


            $newTag->category()->associate($category->fresh());
        }

        $newTag->save();
    }
}
