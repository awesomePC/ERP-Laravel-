<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pjt_invoice_lines', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('transaction_id');
            $table->foreign('transaction_id')
                ->references('id')->on('transactions')
                ->onDelete('cascade');

            $table->string('task');
            $table->text('description')->nullable();
            $table->decimal('rate', 22, 4);
            $table->integer('tax_rate_id')->nullable()->index();
            $table->decimal('quantity', 22, 4);
            $table->decimal('total', 22, 4);
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
        Schema::dropIfExists('pjt_invoice_lines');
    }
}
