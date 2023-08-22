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
        Schema::create('disponibilities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('day');
            $table->unsignedBigInteger('shift');

            $table->foreign('student')->references('id')->on('users');
            $table->foreign('group_project')->references('id')->on('groups_projects');
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
