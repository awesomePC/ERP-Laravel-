<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIngredientWastePercentColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mfg_recipe_ingredients', function (Blueprint $table) {
            $table->decimal('waste_percent', 22, 4)->default(0)->after('quantity');
        });

        Schema::table('transaction_sell_lines', function (Blueprint $table) {
            $table->decimal('mfg_waste_percent', 22, 4)->default(0)->after('quantity');
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
