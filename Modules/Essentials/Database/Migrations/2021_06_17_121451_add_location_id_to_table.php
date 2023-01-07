<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLocationIdToTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('location_id')
                ->after('id_proof_number')
                ->comment('user primary work location')
                ->nullable();
        });

        Schema::table('essentials_payroll_groups', function (Blueprint $table) {
            $table->integer('location_id')
                ->after('business_id')
                ->comment('payroll for work location')
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
