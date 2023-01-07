<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetMaintenancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_maintenances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('business_id')->index();
            $table->integer('asset_id')->index();
            $table->string('maitenance_id')->nullable();
            $table->string('status')->nullable()->index();
            $table->string('priority')->nullable()->index();
            $table->integer('created_by')->index();
            $table->integer('assigned_to')->index()->nullable();
            $table->text('details')->nullable();
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
        Schema::dropIfExists('asset_maintenances');
    }
}
