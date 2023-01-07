<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWoocommerceSyncLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('woocommerce_sync_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id');
            $table->string('sync_type');
            $table->enum('operation_type', ['created', 'updated'])->nullable();
            $table->longText('data')->nullable();
            $table->longText('details')->nullable();
            $table->integer('created_by');
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
        Schema::dropIfExists('woocommerce_sync_logs');
    }
}
