<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repair_device_models', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')
                    ->references('id')->on('business')
                    ->onDelete('cascade');
                    
            $table->string('name');
            $table->text('repair_checklist')->nullable();

            $table->integer('brand_id')->nullable()->unsigned();
            $table->foreign('brand_id')
                ->references('id')->on('brands');

            $table->integer('device_id')->nullable()->unsigned();
            $table->foreign('device_id')
                ->references('id')->on('categories');

            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')
                ->references('id')->on('users');

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
        Schema::dropIfExists('repair_device_models');
    }
}
