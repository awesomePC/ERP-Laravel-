<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateMfgRecipeIngredientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE mfg_recipes DROP COLUMN ingredients;');

        Schema::create('mfg_recipe_ingredients', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mfg_recipe_id')->unsigned();
            $table->foreign('mfg_recipe_id')->references('id')->on('mfg_recipes')->onDelete('cascade');
            $table->integer('variation_id');
            $table->decimal('quantity', 22, 4)->default(0);
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
        Schema::dropIfExists('mfg_recipe_ingredients');
    }
}
