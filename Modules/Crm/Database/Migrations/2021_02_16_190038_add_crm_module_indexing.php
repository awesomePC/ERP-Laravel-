<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCrmModuleIndexing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('crm_contact_id');
        });

        Schema::table('crm_schedules', function (Blueprint $table) {
            $table->index('business_id');
            $table->index('contact_id');
            $table->index('schedule_type');
            $table->index('notify_type');
        });

        Schema::table('crm_lead_users', function (Blueprint $table) {
            $table->index('contact_id');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->index('converted_by');
        });

        Schema::table('crm_call_logs', function (Blueprint $table) {
            $table->index('business_id');
            $table->index('user_id');
            $table->index('contact_id');
            $table->index('created_by');
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
