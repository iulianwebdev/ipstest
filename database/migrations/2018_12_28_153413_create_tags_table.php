<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            // $table->increments('id');
            $table->integer('id')->primary();
            $table->string('name');
            $table->text('description')->nullable(); 
            $table->integer('category_id')->unsigned()->nullable();
            
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->foreign('category_id')
              ->references('id')->on('categories')
              ->onDelete('cascade');
          });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tags');
    }
}
