<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreFieldsToPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->boolean('is_private')->default(0)->after('is_active');
            $table->boolean('is_one_time')->default(0)->after('is_private');
            $table->boolean('enable_custom_link')->default(0)->after('is_one_time');
            $table->string('custom_link')->nullable()->after('enable_custom_link');
            $table->string('custom_link_text')->nullable()->after('custom_link');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
