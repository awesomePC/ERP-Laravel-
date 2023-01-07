<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_transactions', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')
                ->references('id')->on('business')
                ->onDelete('cascade');

            $table->integer('asset_id')->unsigned()
                ->nullable();
            $table->foreign('asset_id')
                ->references('id')->on('assets')
                ->onDelete('cascade');

            $table->string('transaction_type');
            
            $table->string('ref_no');

            $table->integer('receiver')
                ->comment('id from users table, who receives asset')
                ->unsigned()
                ->nullable();
            $table->foreign('receiver')
                ->references('id')->on('users');

            $table->decimal('quantity', 22, 4);

            $table->dateTime('transaction_datetime');
            $table->date('allocated_upto')->nullable();

            $table->text('reason')
                ->nullable();

            $table->integer('parent_id')
                ->comment('id from asset_transactions table')
                ->unsigned()
                ->nullable();
            $table->foreign('parent_id')
                ->references('id')->on('asset_transactions')
                ->onDelete('cascade');

            $table->integer('created_by')
                ->comment('id from users table, who allocated asset')
                ->unsigned();
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
        Schema::dropIfExists('asset_transactions');
    }
}
