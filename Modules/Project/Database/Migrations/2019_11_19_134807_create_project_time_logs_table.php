<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectTimeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pjt_project_time_logs', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('project_id');
            $table->foreign('project_id')
                ->references('id')->on('pjt_projects')
                ->onDelete('cascade');

            $table->unsignedInteger('project_task_id')->nullable();
            $table->foreign('project_task_id')
                ->references('id')->on('pjt_project_tasks')
                ->onDelete('cascade');

            $table->integer('user_id')->index();
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->text('note')->nullable();
            $table->integer('created_by')->index();
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
        Schema::dropIfExists('pjt_project_time_logs');
    }
}
