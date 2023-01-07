<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEssentialsUserSalesTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('essentials_user_sales_targets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->decimal('target_start', 22, 4)->default(0);
            $table->decimal('target_end', 22, 4)->default(0);
            $table->decimal('commission_percent', 22, 4)->default(0);
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
        Schema::dropIfExists('essentials_user_sales_targets');
    }
}
