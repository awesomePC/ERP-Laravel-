<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

class AddRepairPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Permission::create(['name' => 'repair.create']);
        Permission::create(['name' => 'repair.update']);
        Permission::create(['name' => 'repair.view']);
        Permission::create(['name' => 'repair.delete']);

        Permission::create(['name' => 'repair_status.update']);
        Permission::create(['name' => 'repair_status.access']);
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
