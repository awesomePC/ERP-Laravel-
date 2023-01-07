<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Essentials\Entities\ToDo;
use Spatie\Permission\Models\Permission;

class AddMoreColumnsToEssentialsToDosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('essentials_to_dos', function (Blueprint $table) {
            $table->date('end_date')->nullable()->after('date');
            $table->string('task_id')->nullable()->after('end_date');
            $table->text('description')->nullable()->after('task_id');
            $table->string('status')->nullable()->after('description')->index();
            $table->string('estimated_hours')->nullable()->after('status');
            $table->string('priority')->nullable()->after('estimated_hours')->index();
            $table->integer('created_by')->nullable()->after('priority')->index();
        });

        Schema::create('essentials_todos_users', function (Blueprint $table) {
            $table->integer('todo_id');
            $table->integer('user_id');
        });

        //Modify status column of the existing todos
        ToDo::where('is_completed', 1)
            ->update(['status' => 'completed']);

        //move user_id from essentials_to_dos table to essentials_todos_users table
        $todos_users = ToDo::select('id as todo_id', 'user_id')->get()->toArray();
        if (!empty($todos_users)) {
            DB::table('essentials_todos_users')->insert($todos_users);
        }

        //Drop columns user_id and is_completed from essentials_to_dos table
        DB::statement("ALTER TABLE essentials_to_dos DROP COLUMN is_completed, DROP COLUMN user_id");

        Permission::create(['name' => 'essentials.assign_todos']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('essentials_todos_users');
    }
}
