<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayrollGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('essentials_payroll_groups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('business_id');
            $table->string('name');
            $table->string('status');
            $table->string('payment_status')
                ->default('due');
            $table->decimal('gross_total', 22, 4)
                ->default('0');
            $table->integer('created_by');
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
        Schema::dropIfExists('essentials_payroll_groups');
    }
}
