<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddRowsToSystemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('system')->insert(
            [
                ['key'=>'superadmin_version', 'value' => config('superadmin.module_version')],
                ['key'=>'app_currency_id', 'value' => 2],
                ['key'=>'invoice_business_name', 'value' => env('APP_NAME')],
                ['key'=>'invoice_business_landmark', 'value' => 'Landmark'],
                ['key'=>'invoice_business_zip', 'value' => 'Zip'],
                ['key'=>'invoice_business_state', 'value' => 'State'],
                ['key'=>'invoice_business_city', 'value' => 'City'],
                ['key'=>'invoice_business_country', 'value' => 'Country'],
                ['key'=>'email', 'value' => 'superadmin@example.com']
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('system', function (Blueprint $table) {
        });
    }
}
