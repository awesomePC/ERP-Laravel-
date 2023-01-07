<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')
                    ->references('id')->on('business')
                    ->onDelete('cascade');

            $table->string('asset_code');
            $table->string('name');

            $table->decimal('quantity', 22, 4);

            $table->string('model')
                ->nullable();
            $table->string('serial_no')->nullable();

            $table->integer('category_id')
                ->nullable()->unsigned();
            $table->foreign('category_id')
                ->references('id')->on('categories');

            $table->integer('location_id')
                ->nullable()->unsigned();

            $table->date('purchase_date')
                ->nullable();
            $table->string('purchase_type')->nullable();

            $table->decimal('unit_price', 22, 4);

            $table->decimal('depreciation', 22, 4)
                ->nullable();

            $table->boolean('is_allocatable')->default(false);

            $table->text('description')
                ->nullable();

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
        Schema::dropIfExists('assets');
    }
}
