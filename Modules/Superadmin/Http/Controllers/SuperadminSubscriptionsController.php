<?php

namespace Modules\Superadmin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Superadmin\Entities\Subscription;
use Modules\Superadmin\Entities\Package;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

use App\Utils\BusinessUtil;

use App\System;

class SuperadminSubscriptionsController extends BaseController
{
    protected $businessUtil;

    /**
     * Constructor
     *
     * @param BusinessUtil $businessUtil
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil)
    {
        $this->businessUtil = $businessUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $superadmin_subscription = Subscription::join('business', 'subscriptions.business_id', '=', 'business.id')
                ->join('packages', 'subscriptions.package_id', '=', 'packages.id')
                ->select('business.name as business_name', 'packages.name as package_name', 'subscriptions.status', 'subscriptions.start_date', 'subscriptions.trial_end_date', 'subscriptions.end_date', 'subscriptions.package_price', 'subscriptions.paid_via', 'subscriptions.payment_transaction_id', 'subscriptions.id');
            
            return DataTables::of($superadmin_subscription)
                        ->addColumn(
                            'action',
                            '<button data-href ="{{action(\'\Modules\Superadmin\Http\Controllers\SuperadminSubscriptionsController@edit\',[$id])}}" class="btn btn-info btn-xs change_status" data-toggle="modal" data-target="#statusModal">
                            @lang( "superadmin::lang.status")
                            </button> <button data-href ="{{action(\'\Modules\Superadmin\Http\Controllers\SuperadminSubscriptionsController@editSubscription\',["id" => $id])}}" class="btn btn-primary btn-xs btn-modal" data-container=".view_modal">
                            @lang( "messages.edit")
                            </button>'
                        )
                        ->editColumn('trial_end_date', '@if(!empty($trial_end_date)){{@format_date($trial_end_date)}} @endif')
                        ->editColumn('start_date', '@if(!empty($start_date)){{@format_date($start_date)}}@endif')
                        ->editColumn('end_date', '@if(!empty($end_date)){{@format_date($end_date)}}@endif')
                        ->editColumn(
                            'status',
                            '@if($status == "approved")
                                <span class="label bg-light-green">{{__(\'superadmin::lang.\'.$status)}}
                                </span>
                            @elseif($status == "waiting")
                                <span class="label bg-aqua">{{__(\'superadmin::lang.\'.$status)}}
                                </span>
                            @else($status == "declined")
                                <span class="label bg-red">{{__(\'superadmin::lang.\'.$status)}}
                                </span>
                            @endif'
                        )
                        ->editColumn(
                            'package_price',
                            '<span class="display_currency" data-currency_symbol="true">
                                {{$package_price}}
                            </span>'
                        )
                        ->removeColumn('id')
                        ->rawColumns([2, 6, 9])
                        ->make(false);
        }
        return view('superadmin::superadmin_subscription.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->input('business_id');
        $packages = Package::active()->orderby('sort_order')->pluck('name', 'id');

        $gateways = $this->_payment_gateways();

        return view('superadmin::superadmin_subscription.add_subscription')
              ->with(compact('packages', 'business_id', 'gateways'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('subscribe')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $input = $request->only(['business_id', 'package_id', 'paid_via', 'payment_transaction_id']);
            $package = Package::find($input['package_id']);
            $user_id = $request->session()->get('user.id');

            $subscription =  $this->_add_subscription($input['business_id'], $package, $input['paid_via'], $input['payment_transaction_id'], $user_id, true);

            DB::commit();

            $output = ['success' => 1,
                    'msg' => __('lang_v1.success')
                ];
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0, 'msg' => __('messages.something_went_wrong') ];
        }

        return back()->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('superadmin::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $status = Subscription::package_subscription_status();
            $subscription = Subscription::find($id);

            return view('superadmin::superadmin_subscription.edit')
                        ->with(compact('subscription', 'status'));
        }
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['status', 'payment_transaction_id']);
        
                $subscriptions = Subscription::findOrFail($id);

                if ($subscriptions->status != 'approved' && empty($subscriptions->start_date) && $input['status'] == 'approved') {
                    $dates = $this->_get_package_dates($subscriptions->business_id, $subscriptions->package);
                    $subscriptions->start_date = $dates['start'];
                    $subscriptions->end_date = $dates['end'];
                    $subscriptions->trial_end_date = $dates['trial'];
                }

                $subscriptions->status = $input['status'];
                $subscriptions->payment_transaction_id = $input['payment_transaction_id'];
                $subscriptions->save();

                $output = array('success' => true,
                                    'msg' => __("superadmin::lang.subcription_updated_success")
                                );
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = array('success' => false,
                            'msg' => __("messages.something_went_wrong")
                            );
            }
            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function editSubscription($id)
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $subscription = Subscription::find($id);

            return view('superadmin::superadmin_subscription.edit_date_modal')
                        ->with(compact('subscription'));
        }
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function updateSubscription(Request $request)
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['start_date', 'end_date', 'trial_end_date']);
        
                $subscription = Subscription::findOrFail($request->input('subscription_id'));

                $subscription->start_date = !empty($input['start_date']) ? $this->businessUtil->uf_date($input['start_date']) : null;
                $subscription->end_date = !empty($input['end_date']) ? $this->businessUtil->uf_date($input['end_date']) : null;
                $subscription->trial_end_date = !empty($input['trial_end_date']) ? $this->businessUtil->uf_date($input['trial_end_date']) : null;
                $subscription->save();

                $output = array('success' => true,
                                    'msg' => __("superadmin::lang.subcription_updated_success")
                                );
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = array('success' => false,
                            'msg' => __("messages.something_went_wrong")
                            );
            }
            return $output;
        }
    }
}
