<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Project\Entities\ProjectTransaction;
use App\BusinessLocation;
class SetLocationIdOnExistingInvoice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $transactions = ProjectTransaction::where('transactions.type', 'sell')
                        ->where('transactions.sub_type', 'project_invoice')
                        ->get();

        if (!empty($transactions)) {
            foreach ($transactions as $key => $transaction) {
                $business_id = $transaction->business_id;
                $business_locations = BusinessLocation::where('business_id', $business_id)
                    ->get();
                $location = $business_locations->pull(0);
                if (!empty($location)) {
                    ProjectTransaction::where('business_id', $business_id)
                            ->where('id', $transaction->id)
                            ->update(['location_id' => $location->id]);
                }
            }
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
