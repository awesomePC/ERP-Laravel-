<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crm_schedule_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('schedule_id');
            $table->foreign('schedule_id')
                ->references('id')->on('crm_schedules')
                ->onDelete('cascade');
                
            $table->enum('log_type', ['call', 'sms', 'meeting', 'email'])->default('email');
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');

            $table->string('subject');
            $table->text('description')->nullable();
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
        Schema::dropIfExists('crm_schedule_logs');
    }
}
