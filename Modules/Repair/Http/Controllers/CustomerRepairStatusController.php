<?php

namespace Modules\Repair\Http\Controllers;

use Modules\Repair\Entities\JobSheet;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Spatie\Activitylog\Models\Activity;

class CustomerRepairStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('repair::customer_repair.index');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function postRepairStatus(Request $request)
    {
        if ($request->ajax()) {
            try {
                $search_type = $request->input('search_type'); //job_sheet/invoice/mobile
                $search_number = $request->input('search_number'); //job_sheet_no/invoice_no/mobile_num

                $query = JobSheet::leftJoin('transactions',
                            'transactions.repair_job_sheet_id', '=', 'repair_job_sheets.id')
                            ->join('contacts', 'repair_job_sheets.contact_id', '=', 'contacts.id')
                            ->leftJoin(
                                'repair_statuses AS rs',
                                'repair_job_sheets.status_id',
                                '=',
                                'rs.id'
                            )
                            ->leftJoin(
                                'brands AS b',
                                'repair_job_sheets.brand_id',
                                '=',
                                'b.id'
                                )
                            ->leftJoin(
                                'repair_device_models as rdm',
                                'rdm.id',
                                '=',
                                'repair_job_sheets.device_model_id'
                            )
                            ->leftJoin(
                                'categories as device',
                                'device.id',
                                '=',
                                'repair_job_sheets.device_id'
                            );

                if (!empty($search_type) && $search_type == 'job_sheet_no') {
                    $query->where('repair_job_sheets.job_sheet_no', $search_number);
                } elseif (!empty($search_type) && $search_type == 'invoice_no') {
                    $query->where('transactions.invoice_no', $search_number);
                } elseif (!empty($search_type) && $search_type == 'mobile_num') {
                    $query->where('contacts.mobile', $search_number);
                }

                if (!empty($request->input('serial_no'))) {
                    $query->where('repair_job_sheets.serial_no', $request->input('serial_no'));
                }

                $sells = $query->select(
                            'repair_job_sheets.*',
                            'rs.name as repair_status',
                            'rs.color as repair_status_color',
                            'rdm.name as repair_model',
                            'device.name as repair_device',
                            'b.name as manufacturer'
                        )
                        ->groupBy('repair_job_sheets.id')
                        ->get();

                if (count($sells) < 1) {
                    return ['success' => false,
                        'msg' => __("repair::lang.invalid_repair_details")
                    ];
                }

                foreach ($sells as $key => $sell) {
                    $sells[$key]['activities'] = Activity::forSubject($sell)
                                           ->with(['causer', 'subject'])
                                           ->latest()
                                           ->get();
                }

                $repair_html = View::make('repair::customer_repair.repair_details')
                                ->with(compact('sells'))
                                ->render();

                $output = ['success' => true,
                    'msg' => __("lang_v1.success"),
                    'repair_html' => $repair_html
                ];
            } catch (Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }
}
