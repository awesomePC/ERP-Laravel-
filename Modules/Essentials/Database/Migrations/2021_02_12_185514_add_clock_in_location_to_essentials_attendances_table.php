<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClockInLocationToEssentialsAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('essentials_attendances', function (Blueprint $table) {
            $table->text('clock_in_location')
                ->after('clock_out_note')
                ->nullable();
            $table->text('clock_out_location')
                ->after('clock_in_location')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
