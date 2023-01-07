<?php

use App\Transaction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddPayrollColumnsToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('essentials_duration', 8, 2)->deafult(0)->after('created_by');
            $table->string('essentials_duration_unit', 20)->nullable()->after('essentials_duration');
            $table->decimal('essentials_amount_per_unit_duration', 22, 4)->default(0)->after('essentials_duration_unit');
            $table->text('essentials_allowances')->nullable()->after('essentials_amount_per_unit_duration');
            $table->text('essentials_deductions')->nullable()->after('essentials_allowances');
        });

        $payrolls = DB::table('essentials_payrolls')->get();

        $payroll_data = [];

        foreach ($payrolls as $payroll) {
            $payroll_data[] = [
                'expense_for' => $payroll->user_id,
                'business_id' => $payroll->business_id,
                'ref_no' => $payroll->ref_no,
                'transaction_date' => $payroll->year . '-' . str_pad($payroll->month, 2, '0', STR_PAD_LEFT) . '-01' . ' 00:00:00',
                'essentials_duration' => $payroll->duration,
                'essentials_duration_unit' => $payroll->duration_unit,
                'essentials_amount_per_unit_duration' => $payroll->amount_per_unit_duration,
                'essentials_allowances' => $payroll->allowances,
                'essentials_deductions' => $payroll->deductions,
                'type' => 'payroll',
                'status' => 'final',
                'payment_status' => 'due',
                'total_before_tax' => $payroll->gross_amount,
                'final_total' => $payroll->gross_amount,
                'created_by' => $payroll->created_by,
                'created_at' => $payroll->created_at,
                'updated_at' => $payroll->updated_at
            ];
        }

        if (!empty($payroll_data)) {
            Transaction::insert($payroll_data);
        }

        DB::statement("DROP TABLE essentials_payrolls;");
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
