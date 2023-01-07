<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddRecursiveFieldsToCrmSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE crm_schedules MODIFY COLUMN start_datetime DATETIME NULL;");
        DB::statement("ALTER TABLE crm_schedules MODIFY COLUMN end_datetime DATETIME NULL;");
        DB::statement("ALTER TABLE crm_schedules DROP FOREIGN KEY crm_schedules_contact_id_foreign, MODIFY contact_id INT(10) NULL;");

        Schema::table('crm_schedules', function (Blueprint $table) {
            $table->boolean('is_recursive')->default(0)->after('created_by');
            $table->string('follow_up_by_value')->nullable()->after('follow_up_by');
            $table->integer('recursion_days')->nullable()->after('is_recursive');
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
