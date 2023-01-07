<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRepairModuleIndexing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->index('repair_warranty_id');
            $table->index('repair_brand_id');
            $table->index('repair_status_id');
            $table->index('repair_device_id');
            $table->index('repair_job_sheet_id');
        });

        Schema::table('repair_device_models', function (Blueprint $table) {
            $table->index('business_id');
            $table->index('brand_id');
            $table->index('device_id');
            $table->index('created_by');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('repair_model_id');
        });

        Schema::table('repair_job_sheets', function (Blueprint $table) {
            $table->index('business_id');
            $table->index('location_id');
            $table->index('contact_id');
            $table->index('brand_id');
            $table->index('device_id');
            $table->index('device_model_id');
            $table->index('status_id');
            $table->index('service_staff');
            $table->index('created_by');
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
