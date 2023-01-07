<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMfgRecipesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mfg_recipes', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('product_id')->index();
            $table->integer('variation_id')->index();
            $table->text('ingredients');
            $table->text('instructions')->nullable();
            $table->decimal('waste_percent', 10, 2)->default(0);
            $table->decimal('ingredients_cost', 22, 4)->default(0);
            $table->decimal('extra_cost', 22, 4)->default(0);
            $table->decimal('total_quantity', 22, 4)->default(0);
            $table->decimal('final_price', 22, 4);
            $table->integer('sub_unit_id')->nullable();

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
        Schema::dropIfExists('mfg_recipes');
    }
}
