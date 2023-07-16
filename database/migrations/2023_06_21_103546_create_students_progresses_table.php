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
            $table->unsignedBigInteger('student');
            $table->unsignedBigInteger('group_project');
            $table->double('score')->default(0);
            $table->double('performance')->default(0);
            $table->double('quality')->default(0);
            $table->double('technology')->default(0);
            $table->double('deadline')->default(0);
            $table->double('efficiency')->default(0);
            
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
