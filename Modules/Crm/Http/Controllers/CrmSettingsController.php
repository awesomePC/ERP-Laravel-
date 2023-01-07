<?php

namespace Modules\Crm\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Business;
use Modules\Crm\Utils\CrmUtil;
use App\Utils\ModuleUtil;

class CrmSettingsController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $crmUtil;
    protected $moduleUtil;


    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(CrmUtil $crmUtil, ModuleUtil $moduleUtil)
    {
        $this->crmUtil = $crmUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'crmr_module')))) {
            abort(403, 'Unauthorized action.');
        }

        $crm_settings = $this->crmUtil->getCrmSettings($business_id);

        return view('crm::settings.index')
                ->with(compact('crm_settings'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function updateSettings(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module')))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['order_request_prefix']);

            if ($request->has('enable_order_request')) {
                $input['enable_order_request'] = 1;
            }

            Business::where('id', $business_id)
                        ->update(['crm_settings' => json_encode($input)]);

            $output = ['success' => true,
                            'msg' => __("lang_v1.updated_success")
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return redirect()->back()->with(['status' => $output]);
    }
}
