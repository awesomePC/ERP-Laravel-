<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRepairColumnsToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->datetime('repair_completed_on')->nullable()->after('created_by');
            
            $table->integer('repair_warranty_id')->nullable()->after('repair_completed_on');

            $table->integer('repair_brand_id')->nullable()->after('repair_warranty_id');
            $table->integer('repair_status_id')->nullable()->after('repair_brand_id');
            $table->integer('repair_model_id')->nullable()->after('repair_status_id')->index();
            $table->text('repair_defects')->nullable()->after('repair_model_id');
            $table->string('repair_serial_no')->nullable()->after('repair_defects');
            $table->text('repair_checklist')->nullable()->after('repair_serial_no');
            $table->string('repair_security_pwd')->nullable()->after('repair_checklist');
            $table->string('repair_security_pattern')->nullable()->after('repair_security_pwd');

            $table->dateTime('repair_due_date')
                ->after('repair_security_pattern')
                ->nullable();

            $table->integer('repair_device_id')
                ->after('repair_due_date')
                ->nullable();

            $table->boolean('repair_updates_notif')->default(0)->after('repair_device_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
