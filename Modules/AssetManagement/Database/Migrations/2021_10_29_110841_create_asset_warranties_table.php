<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetWarrantiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_warranties', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('asset_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('additional_cost', 22, 4)->default(0);
            $table->text('additional_note')->nullable();
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
        Schema::dropIfExists('asset_warranties');
    }
}
