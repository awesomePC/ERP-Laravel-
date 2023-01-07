<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crm_schedules', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')
                    ->references('id')->on('business')
                    ->onDelete('cascade');

            $table->integer('contact_id')->unsigned();
            $table->foreign('contact_id')
                    ->references('id')->on('contacts')
                    ->onDelete('cascade');

            $table->string('title');
            $table->string('status')->nullable();
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->text('description')->nullable();
            $table->enum('schedule_type', ['call', 'sms', 'meeting', 'email'])->default('email');
            $table->boolean('allow_notification')->default(1);
            $table->text('notify_via')->nullable();
            $table->integer('notify_before')->nullable();
            $table->enum('notify_type', ['minute', 'hour', 'day'])->default('hour');
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
        Schema::dropIfExists('crm_schedules');
    }
}
