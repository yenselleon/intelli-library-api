<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBooksTable extends Migration
{
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('author_id');
            $table->unsignedSmallInteger('published_year')->nullable();
            $table->timestamps();

            $table->foreign('author_id')
                  ->references('id')
                  ->on('authors')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('books');
    }
}
