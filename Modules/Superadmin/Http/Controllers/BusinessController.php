<?php

namespace Modules\Superadmin\Http\Controllers;

use App\Business;
use App\Product;
use App\Transaction;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\VariationLocationDetails;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Superadmin\Notifications\PasswordUpdateNotification;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;
use Modules\Superadmin\Entities\Package;

class BusinessController extends BaseController
{
    protected $businessUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
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
            $date_today = \Carbon::today();
            $businesses = Business::leftjoin('subscriptions AS s', function($join) use ($date_today){
                                $join->on('business.id', '=', 's.business_id')
                                    ->whereDate('s.start_date', '<=', $date_today)
                                    ->whereDate('s.end_date', '>=', $date_today)
                                    ->where('s.status', 'approved');
                            })
                            ->leftjoin('packages as p', 's.package_id', '=', 'p.id' )
                            ->leftjoin('business_locations as bl', 'business.id', '=', 'bl.business_id' )
                            ->leftjoin('users as u', 'u.id', '=', 'business.owner_id')
                            ->leftjoin('users as creator', 'creator.id', '=', 'business.created_by')
                            ->select(
                                    'business.id', 
                                    'business.name',
                                    DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as owner_name"),
                                    'u.email as owner_email',
                                    'u.contact_number',
                                    'bl.mobile',
                                    'bl.alternate_number',
                                    'bl.city',
                                    'bl.state',
                                    'bl.country',
                                    'bl.landmark',
                                    'bl.zip_code',
                                    'business.is_active',
                                    's.start_date',
                                    's.end_date',
                                    'p.name as package_name',
                                    'business.created_at',
                                    DB::raw("CONCAT(COALESCE(creator.surname, ''), ' ', COALESCE(creator.first_name, ''), ' ', COALESCE(creator.last_name, '')) as biz_creator")
                                )->groupBy('business.id');

            if (!empty(request()->package_id)) {
                $businesses->where('p.id', request()->package_id);
            }

            $subscription_status = request()->subscription_status;
            if ($subscription_status == 30) {
                $businesses->whereDate('s.end_date', '<=', \Carbon::today()->addDays(30));
            } else if ($subscription_status == 7) {
                $businesses->whereDate('s.end_date', '<=', \Carbon::today()->addDays(7));
            } else if ($subscription_status == 3) {
                $businesses->whereDate('s.end_date', '<=', \Carbon::today()->addDays(3));
            } elseif ($subscription_status == 'expired') {
                $businesses->where( function($q){
                    $q->whereDate('s.end_date', '<', \Carbon::today())
                    ->orWhereNull('s.end_date');
                });
            } else if ($subscription_status == 'subscribed') {
                $businesses->whereNotNull('s.start_date');
            }

            $is_active = request()->is_active;
            if ($is_active == 'active') {
                $businesses->where('business.is_active', 1);
            } else if ($is_active == 'inactive') {
                $businesses->where('business.is_active', 0);
            }

            $last_transaction_date = request()->last_transaction_date;
            $query = $this->filterTransactionDate($businesses, $last_transaction_date, '>');

            $no_transaction_since = request()->no_transaction_since;

            $query = $this->filterTransactionDate($businesses, $no_transaction_since, '=');

            return Datatables::of($query)
                ->addColumn( 'address', '{{$city}}, {{$state}}, {{$country}} {{$landmark}}, {{$zip_code}}')
                ->addColumn( 'business_contact_number', '{{$mobile}} @if(!empty($alternate_number)), {{$alternate_number}}@endif')
                ->editColumn( 'is_active', '@if($is_active == 1) <span class="label bg-green">@lang("business.is_active")</span> @else <span class="label bg-gray">@lang("lang_v1.inactive")</span> @endif')
                ->addColumn('action', function($row) {
                    $html = '<a href="' . 
                            action("\Modules\Superadmin\Http\Controllers\BusinessController@show", [$row->id]) . '"
                                class="btn btn-info btn-xs">' . __('superadmin::lang.manage' ) . '</a>
                            <button type="button" class="btn btn-primary btn-xs btn-modal" data-href="' . action('\Modules\Superadmin\Http\Controllers\SuperadminSubscriptionsController@create', ['business_id' => $row->id]) . '" data-container=".view_modal">'
                                  . __('superadmin::lang.add_subscription' ) . '</button>';

                            if($row->is_active == 1) {
                                $html .= ' <a href="' . action('\Modules\Superadmin\Http\Controllers\BusinessController@toggleActive', [$row->id, 0]) . '"
                                    class="btn btn-danger btn-xs link_confirmation">' . __('lang_v1.deactivate') . '
                                </a>';
                            } else {
                                $html .= ' <a href="' . action('\Modules\Superadmin\Http\Controllers\BusinessController@toggleActive', [$row->id, 1]) . '"
                                    class="btn btn-success btn-xs link_confirmation">' . __('lang_v1.activate' ) . '
                                </a>';
                            }

                            if(request()->session()->get('user.business_id') != $row->id) {
                                $html .= ' <a href="' . action('\Modules\Superadmin\Http\Controllers\BusinessController@destroy', [$row->id]) . '"
                                    class="btn btn-danger btn-xs delete_business_confirmation">' . __('messages.delete' ) . '</a>';
                            }

                    return $html;
                })
                ->filterColumn('owner_name', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->filterColumn('address', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(city, ''), ', ', COALESCE(state, ''), ', ', COALESCE(country, ''), ', ', COALESCE(landmark, ''), ', ', COALESCE(zip_code, '')) like ?", ["%{$keyword}%"]);
                })
                ->filterColumn('business_contact_number', function ($query, $keyword) {
                    $query->where(function($q) use ($keyword){
                        $q->where('bl.mobile', 'like', "%{$keyword}%")
                        ->orWhere('bl.alternate_number', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('current_subscription', '{{$package_name ?? ""}} @if(!empty($start_date) && !empty($end_date)) ({{@format_date($start_date)}} - {{@format_date($end_date)}}) @endif')
                ->editColumn('created_at', '{{@format_datetime($created_at)}}')
                ->rawColumns(['action', 'is_active', 'created_at'])
                ->make(true);
        }

        $business_id = request()->session()->get('user.business_id');

        $packages = Package::listPackages()->pluck('name', 'id');

        $subscription_statuses = [
            'subscribed' => __('superadmin::lang.subscribed'),
            'expired' => __('report.expired'),
            '30' => __('superadmin::lang.expiring_in_one_month'),
            '7' => __('superadmin::lang.expiring_in_7_days'),
            '3' => __('superadmin::lang.expiring_in_3_days'),
        ];

        $last_transaction_date = [
            'today' => __('home.today'),
            'yesterday' => __('superadmin::lang.yesterday'),
            'this_week' => __('home.this_week'),
            'this_month' => __('home.this_month'),
            'last_month' => __('superadmin::lang.last_month'),
            'this_year' => __('superadmin::lang.this_year'),
            'last_year' => __('superadmin::lang.last_year')
        ];

        return view('superadmin::business.index')
            ->with(compact('business_id', 'packages', 'subscription_statuses', 'last_transaction_date'));
    }

    private function filterTransactionDate($query, $filter, $operator)
    {
        if ($filter == 'today') {
            $today = \Carbon::today()->format('Y-m-d');
            $query->whereRaw("(SELECT COUNT(id) FROM transactions as t WHERE t.business_id = business.id AND DATE(t.transaction_date) = '$today') $operator 0");
        } else if ($filter == 'yesterday') {
            $yesterday = \Carbon::yesterday()->format('Y-m-d');
            $query->whereRaw("(SELECT COUNT(id) FROM transactions as t WHERE t.business_id = business.id AND DATE(t.transaction_date) >= '$yesterday') $operator 0");
        } else if ($filter == 'this_week') {
            $this_week = \Carbon::today()->subDays(7)->format('Y-m-d');
            $query->whereRaw("(SELECT COUNT(id) FROM transactions as t WHERE t.business_id = business.id AND DATE(t.transaction_date) >= '$this_week') $operator 0");
        } else if ($filter == 'this_month') {
            $this_month = \Carbon::today()->firstOfMonth()->format('Y-m-d');
            $query->whereRaw("(SELECT COUNT(id) FROM transactions as t WHERE t.business_id = business.id AND DATE(t.transaction_date) >= '$this_month') $operator 0");
        } else if ($filter == 'last_month') {
            $last_month = \Carbon::today()->subDays(30)->firstOfMonth()->format('Y-m-d');
            $query->whereRaw("(SELECT COUNT(id) FROM transactions as t WHERE t.business_id = business.id AND DATE(t.transaction_date) >= '$last_month') $operator 0");
        } else if ($filter == 'this_year') {
            $this_year = \Carbon::today()->firstOfYear()->format('Y-m-d');
            $query->whereRaw("(SELECT COUNT(id) FROM transactions as t WHERE t.business_id = business.id AND DATE(t.transaction_date) >= '$this_year') $operator 0");
        } else if ($filter == 'last_year') {
            $last_year = \Carbon::today()->subYear()->firstOfYear()->format('Y-m-d');
            $query->whereRaw("(SELECT COUNT(id) FROM transactions as t WHERE t.business_id = business.id AND DATE(t.transaction_date) >= '$last_year') $operator 0");
        }

        return $query;
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        $currencies = $this->businessUtil->allCurrencies();
        $timezone_list = $this->businessUtil->allTimeZones();

        $accounting_methods = $this->businessUtil->allAccountingMethods();

        $months = [];
        for ($i=1; $i<=12 ; $i++) {
            $months[$i] = __('business.months.' . $i);
        }

        $is_admin = true;

        $packages = Package::active()->orderby('sort_order')->pluck('name', 'id');
        $gateways = $this->_payment_gateways();

        return view('superadmin::business.create')
            ->with(compact(
                'currencies',
                'timezone_list',
                'accounting_methods',
                'months',
                'is_admin',
                'packages',
                'gateways'
            ));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            //Create owner.
            $owner_details = $request->only(['surname', 'first_name', 'last_name', 'username', 'email', 'password']);
            $owner_details['language'] = env('APP_LOCALE');
            
            $user = User::create_user($owner_details);

            $business_details = $request->only(['name', 'start_date', 'currency_id', 'tax_label_1', 'tax_number_1', 'tax_label_2', 'tax_number_2', 'time_zone', 'accounting_method', 'fy_start_month']);

            $business_location = $request->only(['name', 'country', 'state', 'city', 'zip_code', 'landmark', 'website', 'mobile', 'alternate_number']);
                
            //Create the business
            $business_details['owner_id'] = $user->id;
            if (!empty($business_details['start_date'])) {
                $business_details['start_date'] = $this->businessUtil->uf_date($business_details['start_date']);
            }
                
            //upload logo
            $logo_name = $this->businessUtil->uploadFile($request, 'business_logo', 'business_logos', 'image');
            if (!empty($logo_name)) {
                $business_details['logo'] = $logo_name;
            }
            
            //default enabled modules
            $business_details['enabled_modules'] = ['purchases','add_sale','pos_sale','stock_transfers','stock_adjustment','expenses'];
            
            //created_by
            $business_details['created_by'] = $request->session()->get('user.id');
            
            $business = $this->businessUtil->createNewBusiness($business_details);

            //Update user with business id
            $user->business_id = $business->id;
            $user->save();

            $this->businessUtil->newBusinessDefaultResources($business->id, $user->id);
            $new_location = $this->businessUtil->addLocation($business->id, $business_location);

            //create new permission with the new location
            Permission::create(['name' => 'location.' . $new_location->id ]);

            $subscription_details = $request->only(['package_id', 'paid_via', 'payment_transaction_id']);

            //Add subscription if present
            if (!empty($subscription_details['package_id']) && !empty($subscription_details['paid_via'])) {
                $subscription =  $this->_add_subscription($business->id, $subscription_details['package_id'], $subscription_details['paid_via'], $subscription_details['payment_transaction_id'],$request->session()->get('user.id'), true);
            }
            
            DB::commit();

            //Module function to be called after after business is created
            if (config('app.env') != 'demo') {
                $this->moduleUtil->getModuleData('after_business_created', ['business' => $business]);
            }

            $output = ['success' => 1,
                            'msg' => __('business.business_created_succesfully')
                        ];

            return redirect()
                ->action('\Modules\Superadmin\Http\Controllers\BusinessController@index')
                ->with('status', $output);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = ['success' => 0,
                            'msg' => __('messages.something_went_wrong')
                        ];

            return back()->with('status', $output)->withInput();
        }
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($business_id)
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        $business = Business::with(['currency', 'locations', 'subscriptions', 'owner'])->find($business_id);
        
        $created_id = $business->created_by;

        $created_by = !empty($created_id) ? User::find($created_id) : null;

        return view('superadmin::business.show')
            ->with(compact('business', 'created_by'));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('superadmin::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $notAllowed = $this->businessUtil->notAllowedInDemo();
            if (!empty($notAllowed)) {
                return $notAllowed;
            }

            //Check if logged in busines id is same as deleted business then not allowed.
            $business_id = request()->session()->get('user.business_id');
            if ($business_id == $id) {
                $output = ['success' => 0, 'msg' => __('superadmin.lang.cannot_delete_current_business')];
                return back()->with('status', $output);
            }

            DB::beginTransaction();

            //Delete related products & transactions.
            $products_id = Product::where('business_id', $id)->pluck('id')->toArray();
            if (!empty($products_id)) {
                VariationLocationDetails::whereIn('product_id', $products_id)->delete();
            }
            Transaction::where('business_id', $id)->delete();

            Business::where('id', $id)
                ->delete();

            DB::commit();

            $output = ['success' => 1, 'msg' => __('lang_v1.success')];
            return redirect()
                ->action('\Modules\Superadmin\Http\Controllers\BusinessController@index')
                ->with('status', $output);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = ['success' => 0,
                            'msg' => __('messages.something_went_wrong')
                        ];

            return back()->with('status', $output)->withInput();
        }
    }

    /**
     * Changes the activation status of a business.
     * @return Response
     */
    public function toggleActive(Request $request, $business_id, $is_active)
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        $notAllowed = $this->businessUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            return $notAllowed;
        }
            
        Business::where('id', $business_id)
            ->update(['is_active' => $is_active]);

        $output = ['success' => 1,
                    'msg' => __('lang_v1.success')
                ];
        return back()->with('status', $output);
    }

    /**
     * Shows user list for a particular business
     * @return Response
     */
    public function usersList($business_id)
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $user_id = request()->session()->get('user.id');

            $users = User::where('business_id', $business_id)
                        ->where('id', '!=', $user_id)
                        ->where('is_cmmsn_agnt', 0)
                        ->select(['id', 'username',
                            DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"), 'email']);

            return Datatables::of($users)
                ->addColumn(
                    'role',
                    function ($row) {
                        $role_name = $this->moduleUtil->getUserRoleName($row->id);
                        return $role_name;
                    }
                )
                ->addColumn(
                    'action',
                    '@can("user.update")
                        <a href="#" class="btn btn-xs btn-primary update_user_password" data-user_id="{{$id}}" data-user_name="{{$full_name}}"><i class="glyphicon glyphicon-edit"></i> @lang("superadmin::lang.update_password")</a>
                        &nbsp;
                        @if(!empty($username))
                        {{-- <a href="{{ route("sign-in-as-user",$id) }}?save_current=true" class="btn btn-xs btn-success"><i class="fas fa-sign-in-alt"></i> @lang("lang_v1.login_as_username", ["username" => $username])</a> --}}
                        @endif
                    @endcan'
                )
                ->filterColumn('full_name', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Updates user password from superadmin
     * @return Response
     */
    public function updatePassword(Request $request)
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $notAllowed = $this->businessUtil->notAllowedInDemo();
            if (!empty($notAllowed)) {
                return $notAllowed;
            }
        
            $user = User::findOrFail($request->input('user_id'));
            $user->password = Hash::make($request->input('password'));
            $user->save();

            //Send password update notification
            if ($this->moduleUtil->IsMailConfigured()) {
                $user->notify(new PasswordUpdateNotification($request->input('password')));
            }

            $output = ['success' => 1,
                        'msg' => __("superadmin::lang.password_updated_successfully")
                    ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return $output;
    }
}
