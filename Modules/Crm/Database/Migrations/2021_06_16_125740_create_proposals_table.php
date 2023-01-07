<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProposalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crm_proposals', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')
                ->references('id')->on('business')
                ->onDelete('cascade');

            $table->integer('contact_id')->unsigned();
            $table->foreign('contact_id')
                ->references('id')->on('contacts')
                ->onDelete('cascade');

            $table->text('subject');
            $table->longText('body');
            $table->integer('sent_by')->index();

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
        Schema::dropIfExists('crm_proposals');
    }
}
