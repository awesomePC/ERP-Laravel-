<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOptionsToPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->boolean('bookings')->after('product_count')
                ->default(false)->comment('Enable/Disable bookings');
            
            $table->boolean('kitchen')->after('bookings')
                ->default(false)->comment('Enable/Disable kitchen');

            $table->boolean('order_screen')->after('kitchen')
                ->default(false)->comment('Enable/Disable order_screen');

            $table->boolean('tables')->after('order_screen')
                ->default(false)->comment('Enable/Disable tables');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {
        });
    }
}
