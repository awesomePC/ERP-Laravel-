<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddJobSheetCustomFieldsToRepairJobSheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('repair_job_sheets', function (Blueprint $table) {
            $table->string('custom_field_1')
                ->after('created_by')
                ->nullable();
            $table->string('custom_field_2')
                ->after('custom_field_1')
                ->nullable();
            $table->string('custom_field_3')
                ->after('custom_field_2')
                ->nullable();
            $table->string('custom_field_4')
                ->after('custom_field_3')
                ->nullable();
            $table->string('custom_field_5')
                ->after('custom_field_4')
                ->nullable();
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
