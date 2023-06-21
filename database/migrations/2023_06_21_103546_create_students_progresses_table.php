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
        Schema::create('students_progresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('status');
            $table->integer('score');
            $table->unsignedBigInteger('user');
            $table->unsignedBigInteger('group_project');
            
            $table->foreign('user')->references('id')->on('users');
            $table->foreign('group_project')->references('id')->on('sessions');
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
        Schema::dropIfExists('students_progresses');
    }
};
