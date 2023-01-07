<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuperadminFrontendPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('superadmin_frontend_pages', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('title')->nullable();
            $table->string('slug');
            $table->longtext('content');
            $table->boolean('is_shown')->deafault(1);
            $table->integer('menu_order')->nullable()->default(0);

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
        Schema::dropIfExists('superadmin_frontend_pages');
    }
}
