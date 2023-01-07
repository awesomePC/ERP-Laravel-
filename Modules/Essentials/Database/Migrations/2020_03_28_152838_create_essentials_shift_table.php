<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEssentialsShiftTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('essentials_shifts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');

            $table->enum('type', ['fixed_shift', 'flexible_shift'])
                ->default('fixed_shift');

            $table->integer('business_id');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('holidays')->nullable();
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
        Schema::dropIfExists('essentials_shifts');
    }
}
