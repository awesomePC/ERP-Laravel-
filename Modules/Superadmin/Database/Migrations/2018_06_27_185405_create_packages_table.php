<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description');
            $table->integer('location_count')->comment("No. of Business Locations, 0 = infinite option.");
            $table->integer('user_count');
            $table->integer('product_count');
            $table->integer('invoice_count');
            $table->enum('interval', ['days', 'months', 'years']);
            $table->integer('interval_count');
            $table->integer('trial_days');
            $table->decimal('price', 22, 4);
            $table->integer('created_by');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active');
            $table->softDeletes();
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
        Schema::dropIfExists('packages');
    }
}
