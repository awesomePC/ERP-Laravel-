<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEssentialsModuleIndexing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('essentials_document_shares', function (Blueprint $table) {
            $table->index('document_id');
            $table->index('value_type');
        });

        Schema::table('essentials_reminders', function (Blueprint $table) {
            $table->index('business_id');
            $table->index('user_id');
        });

        Schema::table('essentials_to_dos', function (Blueprint $table) {
            $table->index('business_id');
            $table->index('task_id');
        });

        Schema::table('essentials_messages', function (Blueprint $table) {
            $table->index('business_id');
            $table->index('user_id');
            $table->index('location_id');
        });

        Schema::table('essentials_holidays', function (Blueprint $table) {
            $table->index('location_id');
        });

        Schema::table('essentials_user_allowance_and_deductions', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('allowance_deduction_id', 'allow_deduct_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('essentials_department_id');
            $table->index('essentials_designation_id');
        });

        Schema::table('essentials_shifts', function (Blueprint $table) {
            $table->index('type');
            $table->index('business_id');
        });

        Schema::table('essentials_user_shifts', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('essentials_shift_id');
        });

        Schema::table('essentials_attendances', function (Blueprint $table) {
            $table->index('essentials_shift_id');
        });

        Schema::table('essentials_kb', function (Blueprint $table) {
            $table->index('business_id');
            $table->index('parent_id');
            $table->index('created_by');
        });

        Schema::table('essentials_kb_users', function (Blueprint $table) {
            $table->index('kb_id');
            $table->index('user_id');
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
