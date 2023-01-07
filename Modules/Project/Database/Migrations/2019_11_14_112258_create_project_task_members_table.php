<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectTaskMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pjt_project_task_members', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('project_task_id');
            $table->foreign('project_task_id')
                ->references('id')->on('pjt_project_tasks')
                ->onDelete('cascade');

            $table->integer('user_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pjt_project_task_members');
    }
}
