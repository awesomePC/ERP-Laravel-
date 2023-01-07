<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductionColumnsToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('mfg_parent_production_purchase_id')->nullable()->after('created_by');
            $table->decimal('mfg_wasted_units', 22, 4)->nullable()->after('mfg_parent_production_purchase_id');
            $table->decimal('mfg_production_cost', 22, 4)->default(0)->after('mfg_wasted_units');
            $table->boolean('mfg_is_final')->default(0)->after('mfg_production_cost');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
