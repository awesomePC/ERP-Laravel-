<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIngredientGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mfg_ingredient_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('business_id');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::table('mfg_recipe_ingredients', function (Blueprint $table) {
            $table->integer('mfg_ingredient_group_id')->nullable()->after('variation_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ingredient_groups');
    }
}
