<?php

use Illuminate\Database\Seeder;
use App\Module;

class iPSDevTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(Module::count() > 0) return;

        for ($i = 1; $i <= 7; $i++){
            Module::insert([
                [
                    'course_key' => 'ipa',
                    'name' => 'IPA Module ' . $i
                ],

                [
                    'course_key' => 'iea',
                    'name' => 'IEA Module ' . $i
                ],

                [
                    'course_key' => 'iaa',
                    'name' => 'IAA Module ' . $i
                ]
            ]);
        }


    }
}
