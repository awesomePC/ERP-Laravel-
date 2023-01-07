<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWoocommerceWhOcSecretColumnToBusinessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business', function (Blueprint $table) {
            $table->string('woocommerce_wh_oc_secret')->nullable()->after('woocommerce_api_settings');
            $table->string('woocommerce_wh_ou_secret')->nullable()->after('woocommerce_wh_oc_secret');
            $table->string('woocommerce_wh_od_secret')->nullable()->after('woocommerce_wh_ou_secret');
            $table->string('woocommerce_wh_or_secret')->nullable()->after('woocommerce_wh_od_secret');
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
