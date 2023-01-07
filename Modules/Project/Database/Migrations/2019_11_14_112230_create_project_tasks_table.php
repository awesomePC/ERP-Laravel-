<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pjt_project_tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->index();
            
            $table->unsignedInteger('project_id');
            $table->foreign('project_id')
                ->references('id')->on('pjt_projects')
                ->onDelete('cascade');

            $table->string('task_id');
            $table->string('subject');
            $table->dateTime('start_date')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('low');
            $table->text('description')->nullable();
            $table->integer('created_by')->index();
            $table->enum('status', ['completed', 'not_started', 'in_progress', 'on_hold', 'cancelled'])->default('not_started');
            $table->string('custom_field_1')->nullable();
            $table->string('custom_field_2')->nullable();
            $table->string('custom_field_3')->nullable();
            $table->string('custom_field_4')->nullable();
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
        Schema::dropIfExists('pjt_project_tasks');
    }
}
