<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->tinyInteger('id_author');
            $table->tinyInteger('id_category');
            $table->string('book_language', 20);
            $table->tinyInteger('total_pages');
            $table->text('sinopsis');
            $table->enum('type', ['Non-Fantasy', 'Fantasy']);
            $table->string('publisher', 100);
            $table->date('published_at');
            $table->string('isbn', 13);
            $table->text('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('books');
    }
};
