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
        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->enum('gender', ['Male', 'Female']);
            $table->date('birth_date');
            $table->string('religion', 20);
            $table->string('ethnic', 20);
            $table->string('citizenship', 30);
            $table->string('photo', 255);
            $table->text('hobbies');
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
        Schema::dropIfExists('authors');
    }
};
