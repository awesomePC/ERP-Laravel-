<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

class AddWoocommercePermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Permission::create(['name' => 'woocommerce.syc_categories']);
        Permission::create(['name' => 'woocommerce.sync_products']);
        Permission::create(['name' => 'woocommerce.sync_orders']);
        Permission::create(['name' => 'woocommerce.map_tax_rates']);
        Permission::create(['name' => 'woocommerce.access_woocommerce_api_settings']);
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
