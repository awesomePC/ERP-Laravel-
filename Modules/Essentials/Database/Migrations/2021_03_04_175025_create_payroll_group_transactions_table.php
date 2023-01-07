<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayrollGroupTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('essentials_payroll_group_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('payroll_group_id');
            
            $table->foreign('payroll_group_id')
                ->references('id')->on('essentials_payroll_groups')
                ->onDelete('cascade');
                
            $table->integer('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('essentials_payroll_group_transactions');
    }
}
