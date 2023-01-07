<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSecondaryUnitColumnsToProductsSellLinePurchaseLinesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('secondary_unit_id')->nullable()->after('unit_id')->index();
        });

        Schema::table('purchase_lines', function (Blueprint $table) {
            $table->decimal('secondary_unit_quantity', 22, 4)->default(0)->after('quantity');
        });

        Schema::table('transaction_sell_lines', function (Blueprint $table) {
            $table->decimal('secondary_unit_quantity', 22, 4)->default(0)->after('quantity');
        });

        Schema::table('stock_adjustment_lines', function (Blueprint $table) {
            $table->decimal('secondary_unit_quantity', 22, 4)->default(0)->after('quantity');
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
