<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('essentials_salary', 22, 4)
                ->after('essentials_designation_id')
                ->nullable();

            $table->string('essentials_pay_period')
                ->after('essentials_salary')
                ->nullable();

            $table->string('essentials_pay_cycle')
                ->after('essentials_pay_period')
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
