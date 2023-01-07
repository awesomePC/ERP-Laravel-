<?php

namespace Modules\AssetManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Utils\ModuleUtil;
use Modules\AssetManagement\Utils\AssetUtil;
use App\Business;
use App\NotificationTemplate;
use App\User;

class AssetSettingsController extends Controller
{   
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;
    protected $assetUtil;

    /**
     * Constructor
     *
     */
    public function __construct(ModuleUtil $moduleUtil, AssetUtil $assetUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->assetUtil = $assetUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {   
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'assetmanagement_module')))) {
            abort(403, 'Unauthorized action.');
        }

        $asset_settings = $this->assetUtil->getAssetSettings($business_id);

        $send_for_maintenance_template = NotificationTemplate::where('business_id', 
                                            $business_id)
                                            ->where('template_for', 'send_for_maintenance')
                                            ->first();

        if (empty($send_for_maintenance_template)) {
            $send_for_maintenance_template['subject'] = 'Asset {asset_code} sent for maintaiaince';
            $send_for_maintenance_template['email_body'] = 
                '<p>Asset {asset_code} sent for maintenance by {created_by}</p>
                <p>Maintenance ID: {maintenance_id}</p>
                <p>Status: {status}</p>
                <p>Priority: {priority}</p>
                <p>Details: {details}</p>';
        } else {
            $send_for_maintenance_template->toArray();
        }

        $assigned_for_maintenance_template = NotificationTemplate::where('business_id', 
                                            $business_id)
                                            ->where('template_for', 'assigned_for_maintenance')
                                            ->first();

        if (empty($assigned_for_maintenance_template)) {
            $assigned_for_maintenance_template['subject'] = 'Asset {asset_code} assigned for maintaiaince';
            $assigned_for_maintenance_template['email_body'] = 
                '<p>Asset {asset_code} assigned for maintenance</p>
                <p>Maintenance ID: {maintenance_id}</p>
                <p>Status: {status}</p>
                <p>Priority: {priority}</p>
                <p>Details: {details}</p>';
        } else {
            $assigned_for_maintenance_template->toArray();
        }

        $users = User::forDropdown($business_id, false);

        return view('assetmanagement::settings.index')
            ->with(compact('asset_settings', 'send_for_maintenance_template', 'assigned_for_maintenance_template', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('assetmanagement::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'assetmanagement_module')))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only('asset_code_prefix', 'allocation_code_prefix', 'revoke_code_prefix', 'asset_maintenance_prefix', 'send_for_maintenence_recipients');

            if ($request->has('enable_asset_send_for_maintenance_email')) {
                $input['enable_asset_send_for_maintenance_email'] = 1;
            }

            if ($request->has('enable_asset_assigned_for_maintenance_email')) {
                $input['enable_asset_assigned_for_maintenance_email'] = 1;
            }

            Business::where('id', $business_id)
                ->update(['asset_settings' => json_encode($input)]);

            if (!empty($request->input('send_for_maintenance'))) {
                NotificationTemplate::updateOrCreate([
                        'business_id' => $business_id,
                        'template_for' => 'send_for_maintenance'
                    ],
                    [
                        'email_body' => $request->input('send_for_maintenance')['email_body'],
                        'subject' => $request->input('send_for_maintenance')['subject']
                    ]
                );
            }

            if (!empty($request->input('assigned_for_maintenance'))) {
                NotificationTemplate::updateOrCreate([
                        'business_id' => $business_id,
                        'template_for' => 'assigned_for_maintenance'
                    ],
                    [
                        'email_body' => $request->input('assigned_for_maintenance')['email_body'],
                        'subject' => $request->input('assigned_for_maintenance')['subject']
                    ]
                );
            }
                

            $output = ['success' => true,
                'msg' => __("lang_v1.updated_success")
            ];
        } catch (Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('assetmanagement::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('assetmanagement::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
