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
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->text('description');
            $table->date('date');
            $table->unsignedBigInteger('status');
            $table->unsignedBigInteger('course');
            $table->unsignedBigInteger('student');
            $table->unsignedBigInteger('teacher')->nullable();
            
            $table->foreign('status')->references('id')->on('consultations_statuses');
            $table->foreign('course')->references('id')->on('courses');
            $table->foreign('student')->references('id')->on('users');
            $table->foreign('teacher')->references('id')->on('users');
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
        Schema::dropIfExists('consultations');
    }
};
