<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRemindersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('essentials_reminders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id');
            $table->integer('user_id');
            $table->string('name');
            $table->date('date');
            $table->time('time');
            $table->enum('repeat', ['one_time', 'every_day', 'every_week', 'every_month']);
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
        Schema::dropIfExists('essentials_reminders');
    }
}
