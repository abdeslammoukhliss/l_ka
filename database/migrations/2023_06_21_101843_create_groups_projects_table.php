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
        Schema::create('groups_projects', function (Blueprint $table) {
            $table->id();
            $table->date('affected_date');
            $table->date('deadline');
            $table->unsignedBigInteger('project');
            $table->unsignedBigInteger('group');
            
            $table->foreign('project')->references('id')->on('projects');
            $table->foreign('group')->references('id')->on('groups');
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
        Schema::dropIfExists('groups_projects');
    }
};
