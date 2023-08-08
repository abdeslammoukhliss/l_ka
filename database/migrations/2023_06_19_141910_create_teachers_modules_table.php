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
        Schema::create('teachers_modules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher');
            $table->unsignedBigInteger('module');

            $table->foreign('teacher')->references('id')->on('users');
            $table->foreign('module')->references('id')->on('modules');
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
        Schema::dropIfExists('teachers_modules');
    }
};
