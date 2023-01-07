<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crm_schedule_users', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('schedule_id');
            $table->foreign('schedule_id')
                ->references('id')->on('crm_schedules')
                ->onDelete('cascade');

            $table->integer('user_id')->index();
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
        Schema::dropIfExists('crm_schedule_users');
    }
}
