<?php

namespace Modules\Woocommerce\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use Modules\Woocommerce\Entities\WoocommerceSyncLog;

class AddDummySyncLogTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            array('business_id' => '1','sync_type' => 'categories','operation_type' => 'updated','data' => '["Accessories","Athletic Clothing"]','details' => null,'created_by' => '10','created_at' => '2018-10-31 00:42:06','updated_at' => '2018-10-31 00:42:06'),
            array('business_id' => '1','sync_type' => 'all_products','operation_type' => 'created','data' => '["AS0090","AS0091"]','details' => null,'created_by' => '10','created_at' => '2018-10-31 00:49:16','updated_at' => '2018-10-31 00:49:16'),
            array('business_id' => '1','sync_type' => 'orders','operation_type' => 'updated','data' => '["337"]','details' => null,'created_by' => '10','created_at' => '2018-10-24 03:57:08','updated_at' => '2018-10-24 03:57:08'),
            array('business_id' => '1','sync_type' => 'all_products','operation_type' => null,'data' => '[]','details' => null,'created_by' => '10','created_at' => '2018-10-24 00:03:39','updated_at' => '2018-10-24 00:03:39'),
            array('business_id' => '1','sync_type' => 'orders','operation_type' => 'created','data' => '["342","340"]','details' => '[{"error_type":"order_insuficient_product_qty","order_number":"342","msg":"ERROR: NOT ALLOWED: Mismatch between sold and purchase quantity. Product: Diary of a Wimpy Kid SKU: AS0022 Quantity: 2"},{"error_type":"order_product_not_found","order_number":"340","product":"Test Product - woo-tp"}]','created_by' => '10','created_at' => '2018-10-24 05:12:59','updated_at' => '2018-10-24 05:12:59'),
            array('business_id' => '1','sync_type' => 'orders','operation_type' => 'updated','data' => '["341"]','details' => null,'created_by' => '10','created_at' => '2018-10-24 05:12:59','updated_at' => '2018-10-24 05:12:59'),
            array('business_id' => '1','sync_type' => 'orders','operation_type' => 'created','data' => '["344","342","340"]','details' => '[{"error_type":"order_customer_empty","order_number":"344"},{"error_type":"order_insuficient_product_qty","order_number":"342","msg":"ERROR: NOT ALLOWED: Mismatch between sold and purchase quantity. Product: Diary of a Wimpy Kid SKU: AS0022 Quantity: 2"},{"error_type":"order_product_not_found","order_number":"340","product":"Test Product SKU:woo-tp"}]','created_by' => '10','created_at' => '2018-10-30 05:55:53','updated_at' => '2018-10-30 05:55:53'),
       ];

        WoocommerceSyncLog::insert($data);
    }
}
