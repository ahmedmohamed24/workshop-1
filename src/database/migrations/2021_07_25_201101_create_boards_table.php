<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoardsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('boards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner');
            $table->foreign('owner')->references('id')->on('users')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('color', 7);
            $table->index('slug');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('boards');
    }
}
