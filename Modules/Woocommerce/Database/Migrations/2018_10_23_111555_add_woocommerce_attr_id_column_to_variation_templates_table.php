<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWoocommerceAttrIdColumnToVariationTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('variation_templates', function (Blueprint $table) {
            $table->integer('woocommerce_attr_id')->nullable()->after('business_id');
        });

        Schema::table('variations', function (Blueprint $table) {
            $table->integer('woocommerce_variation_id')->nullable()->after('product_variation_id');
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
