<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrmCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crm_campaigns', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')
                    ->references('id')->on('business')
                    ->onDelete('cascade');

            $table->string('name');
            $table->enum('campaign_type', ['sms', 'email'])->default('email');
            $table->string('subject')->nullable();
            $table->text('email_body')->nullable();
            $table->text('sms_body')->nullable();
            $table->dateTime('sent_on')->nullable();
            $table->text('contact_ids');
            $table->integer('created_by')->index();
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
        Schema::dropIfExists('crm_campaigns');
    }
}
