<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEssentialsPayrollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('essentials_payrolls', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index();
            $table->integer('business_id')->index();
            $table->string('ref_no')->nullable();
            $table->tinyInteger('month');
            $table->smallInteger('year');
            $table->decimal('duration', 8, 2);
            $table->string('duration_unit', 20);
            $table->decimal('amount_per_unit_duration', 22, 4)->default(0);
            $table->text('allowances')->nullable();
            $table->text('deductions')->nullable();
            $table->decimal('gross_amount', 22, 4)->default(0);
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
        Schema::dropIfExists('essentials_payrolls');
    }
}
