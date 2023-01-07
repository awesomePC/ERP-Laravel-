<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContactIdToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'crm_contact_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('crm_contact_id')
                    ->unsigned()->nullable()
                    ->after('status');
    
                $table->foreign('crm_contact_id')
                    ->references('id')->on('contacts')
                    ->onDelete('cascade');
            });
        }
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
