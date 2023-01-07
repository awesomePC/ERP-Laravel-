<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pjt_projects', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->index();
            $table->string('name');
            $table->integer('contact_id')->index()->nullable();
            $table->enum('status', ['not_started', 'in_progress', 'on_hold', 'cancelled', 'completed']);
            $table->integer('lead_id')->index();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->text('description')->nullable();
            $table->integer('created_by')->index();
            $table->text('settings')->nullable();
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
        Schema::dropIfExists('pjt_projects');
    }
}
