<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEssentialsKbTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('essentials_kb', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('business_id')->references('id')->on('business')
                ->onDelete('cascade');
            $table->string('title');
            $table->longText('content')->nullable();
            $table->string('status');
            $table->string('kb_type');
            
            $table->unsignedBigInteger('parent_id')
                ->comment('id from essentials_kb table')
                ->nullable();

            $table->foreign('parent_id')
                ->references('id')->on('essentials_kb')
                ->onDelete('cascade');

            $table->string('share_with')->nullable()->comment('public, private, only_with');

            $table->unsignedBigInteger('created_by');
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
        Schema::dropIfExists('essentials_kb');
    }
}
