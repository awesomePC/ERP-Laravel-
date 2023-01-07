<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDepartmentAndDesignationToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('crm_department')
                ->nullable()
                ->comment("Contact person's department")
                ->after('id_proof_number');

            $table->string('crm_designation')
                ->nullable()
                ->comment("Contact person's designation")
                ->after('crm_department');
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
