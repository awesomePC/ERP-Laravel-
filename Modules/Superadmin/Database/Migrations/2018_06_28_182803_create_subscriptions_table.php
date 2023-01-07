<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->integer('package_id')->unsigned();
            $table->date('start_date')->nullable();
            $table->date('trial_end_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('package_price', 22, 4);
            $table->longText('package_details');
            $table->integer('created_id')->unsigned();
            $table->string('paid_via')->nullable();
            $table->string('payment_transaction_id')->nullable();
            $table->enum('status', ['approved', 'waiting', 'declined'])->default('waiting');
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
        Schema::dropIfExists('subscriptions');
    }
}
