<?php

namespace Modules\Crm\Http\Controllers;

use App\Category;
use App\User;
use App\Utils\ModuleUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Crm\Entities\CrmContact;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Modules\Crm\Utils\CrmUtil;
use App\Contact;

class LeadController extends Controller
{
    protected $commonUtil;
    protected $moduleUtil;
    protected $crmUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil, CrmUtil $crmUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil = $moduleUtil;
        $this->crmUtil = $crmUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        $can_access_all_leads = auth()->user()->can('crm.access_all_leads');
        $can_access_own_leads = auth()->user()->can('crm.access_own_leads');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module')) || !($can_access_all_leads || $can_access_own_leads)) {
            abort(403, 'Unauthorized action.');
        }

        $life_stages = Category::forDropdown($business_id, 'life_stage');
        
        if (is_null(request()->get('lead_view'))) {
            $lead_view = 'list_view';
        } else {
            $lead_view = request()->get('lead_view');
        }

        if (request()->ajax()) {
            
            $leads = $this->crmUtil->getLeadsListQuery($business_id);

            if (!$can_access_all_leads && $can_access_own_leads) {
                $leads->where( function($query) {
                    $query->whereHas('leadUsers', function($q){
                        $q->where('user_id', auth()->user()->id);
                    });
                });
            }

            if (!empty(request()->get('source'))) {
                $leads->where('crm_source', request()->get('source'));
            }

            if (!empty(request()->get('life_stage'))) {
                $leads->where('crm_life_stage', request()->get('life_stage'));
            }

            if (!empty(request()->get('user_id'))) {
                $user_id = request()->get('user_id');
                $leads->where( function($query) use ($user_id) {
                    $query->whereHas('leadUsers', function($q) use ($user_id){
                        $q->where('user_id', $user_id);
                    });
                });
            }
            
            if ($lead_view == 'list_view') {
                return Datatables::of($leads)
                    ->addColumn('address', '{{implode(", ", array_filter([$address_line_1, $address_line_2, $city, $state, $country, $zip_code]))}}')
                    ->addColumn('action', function ($row) {
                        $html = '<div class="btn-group">
                                    <button class="btn btn-info dropdown-toggle btn-xs" type="button"  data-toggle="dropdown" aria-expanded="false">
                                        '. __("messages.action").'
                                        <span class="caret"></span>
                                        <span class="sr-only">'
                                           . __("messages.action").'
                                        </span>
                                    </button>
                                      <ul class="dropdown-menu dropdown-menu-left" role="menu">
                                       <li>
                                            <a href="' . action('\Modules\Crm\Http\Controllers\LeadController@show', ['lead' => $row->id]) . '" class="cursor-pointer view_lead">
                                                <i class="fa fa-eye"></i>
                                                '.__("messages.view").'
                                            </a>
                                        </li>
                                        <li>
                                            <a data-href="' . action('\Modules\Crm\Http\Controllers\LeadController@edit', ['lead' => $row->id]) . '"class="cursor-pointer edit_lead">
                                                <i class="fa fa-edit"></i>
                                                '.__("messages.edit").'
                                            </a>
                                        </li>
                                        <li>
                                            <a data-href="' . action('\Modules\Crm\Http\Controllers\LeadController@convertToCustomer', ['id' => $row->id]) . '" class="cursor-pointer convert_to_customer">
                                                <i class="fas fa-redo"></i>
                                                '.__("crm::lang.convert_to_customer").'
                                            </a>
                                        </li>
                                        <li>
                                            <a data-href="' . action('\Modules\Crm\Http\Controllers\LeadController@destroy', ['lead' => $row->id]) . '" class="cursor-pointer delete_a_lead">
                                                <i class="fas fa-trash"></i>
                                                '.__("messages.delete").'
                                            </a>
                                        </li>';

                        $html .= '</ul>
                                </div>';

                        return $html;
                    })
                    ->addColumn('last_follow_up', function($row) {
                        $html = '';

                        if (!empty($row->last_follow_up)) {
                            $html .= $this->commonUtil->format_date($row->last_follow_up, true);
                            $html .= '<br><a href="'.action('\Modules\Crm\Http\Controllers\ScheduleController@show', ['follow_up' => $row->last_follow_up_id]).'" target="_blank" title="'.__("crm::lang.view_follow_up").'" data-toggle="tooltip">
                                <i class="fas fa-external-link-alt"></i>
                            </a><br>';
                        }

                        $infos = json_decode($row->last_follow_up_additional_info, true);
                        
                        if (!empty($infos)) {
                            foreach ($infos as $key => $value) {
                                $html .= $key .' : '.$value.'<br>';
                            }
                        }

                      return $html ;  
                    })
                    ->orderColumn('last_follow_up', function ($query, $order) {
                        $query->orderBy('last_follow_up', $order);
                    })
                    ->addColumn('upcoming_follow_up', function($row) {
                        $html = '';

                        if (!empty($row->upcoming_follow_up)) {
                            $html .= $this->commonUtil->format_date($row->upcoming_follow_up, true);
                            $html .= '<br><a href="'.action('\Modules\Crm\Http\Controllers\ScheduleController@show', ['follow_up' => $row->upcoming_follow_up_id]).'" target="_blank" title="'.__("crm::lang.view_follow_up").'" data-toggle="tooltip">
                                <i class="fas fa-external-link-alt"></i>
                            </a><br>';
                        }

                        $html .= '<a href="#" data-href="'.action('\Modules\Crm\Http\Controllers\ScheduleController@create', ["schedule_for"=>"lead", "contact_id"=>$row->id]).'" class="btn-modal btn btn-xs btn-primary" data-container=".schedule">
                            <i class="fas fa-plus"></i>'.
                            __("crm::lang.add_schedule").'
                        </a><br>';

                        $infos = json_decode($row->upcoming_follow_up_additional_info, true);
                        
                        if (!empty($infos)) {
                            foreach ($infos as $key => $value) {
                                $html .= $key .' : '.$value.'<br>';
                            }
                        }

                      return $html ;  
                    })
                    ->orderColumn('upcoming_follow_up', function ($query, $order) {
                        $query->orderBy('upcoming_follow_up', $order);
                    })
                    ->editColumn('created_at', '
                        {{@format_date($created_at)}}
                    ')
                    ->editColumn('crm_source', function ($row) {
                        return optional($row->Source)->name;
                    })
                    ->editColumn('crm_life_stage', function ($row) {
                        return optional($row->lifeStage)->name;
                    })
                    ->editColumn('name', '@if(!empty($supplier_business_name)) {{$supplier_business_name}},<br>@endif {{$name}}')
                    ->editColumn('leadUsers', function ($row) {
                        $html = '&nbsp;';
                        foreach ($row->leadUsers as $leadUser) {
                            if (isset($leadUser->media->display_url)) {
                                $html .= '<img class="user_avatar" src="'.$leadUser->media->display_url.'" data-toggle="tooltip" title="'.$leadUser->user_full_name.'">';
                            } else {
                                $html .= '<img class="user_avatar" src="https://ui-avatars.com/api/?name='.$leadUser->first_name.'" data-toggle="tooltip" title="'.$leadUser->user_full_name.'">';
                            }
                        }

                        return $html;
                    })
                    ->removeColumn('id')
                    ->filterColumn('address', function ($query, $keyword) {
                        $query->where( function($q) use ($keyword){
                            $q->where('address_line_1', 'like', "%{$keyword}%")
                            ->orWhere('address_line_2', 'like', "%{$keyword}%")
                            ->orWhere('city', 'like', "%{$keyword}%")
                            ->orWhere('state', 'like', "%{$keyword}%")
                            ->orWhere('country', 'like', "%{$keyword}%")
                            ->orWhere('zip_code', 'like', "%{$keyword}%")
                            ->orWhereRaw("CONCAT(COALESCE(address_line_1, ''), ', ', COALESCE(address_line_2, ''), ', ', COALESCE(city, ''), ', ', COALESCE(state, ''), ', ', COALESCE(country, '') ) like ?", ["%{$keyword}%"]);
                        });
                    })
                    ->rawColumns(['action', 'crm_source', 'crm_life_stage', 'leadUsers', 'last_follow_up', 'upcoming_follow_up', 'created_at', 'name'])
                    ->make(true);
            } elseif ($lead_view == 'kanban') {
                $leads = $leads->get()->groupBy('crm_life_stage');
                //sort leads based on life stage
                $crm_leads = [];
                $board_draggable_to = [];
                foreach ($life_stages as $key => $value) {
                    $board_draggable_to[] = strval($key);
                    if (!isset($leads[$key])) {
                        $crm_leads[strval($key)] = [];
                    } else {
                        $crm_leads[strval($key)] = $leads[$key];
                    }
                }

                $leads_html = [];
                foreach ($crm_leads as $key => $leads) {
                    //get all the leads for particular board(life stage)
                    $cards = [];
                    foreach ($leads as $lead) {
                        $edit = action('\Modules\Crm\Http\Controllers\LeadController@edit', ['lead' => $lead->id]);
                        
                        $delete = action('\Modules\Crm\Http\Controllers\LeadController@destroy', ['lead' => $lead->id]);

                        $view = action('\Modules\Crm\Http\Controllers\LeadController@show', ['lead' => $lead->id]);

                        //if member then get their avatar
                        if ($lead->leadUsers->count() > 0) {
                            $assigned_to = [];
                            foreach ($lead->leadUsers as $member) {
                                if (isset($member->media->display_url)) {
                                    $assigned_to[$member->user_full_name] = $member->media->display_url;
                                } else {
                                    $assigned_to[$member->user_full_name] = "https://ui-avatars.com/api/?name=".$member->first_name;
                                }
                            }
                        }

                        $cards[] = [
                                'id' => $lead->id,
                                'title' => $lead->name,
                                'viewUrl' => $view,
                                'editUrl' => $edit,
                                'editUrlClass' => 'edit_lead',
                                'deleteUrl' => $delete,
                                'deleteUrlClass' => 'delete_a_lead',
                                'assigned_to' => $assigned_to,
                                'hasDescription' => false,
                                'tags' => [$lead->Source->name ?? ''],
                                'dragTo' => $board_draggable_to
                            ];
                    }

                    //get all the card & board title for particular board(life stage)
                    $leads_html[] = [
                        'id' => strval($key),
                        'title' => $life_stages[$key],
                        'cards' => $cards
                    ];
                }

                $output = [
                    'success' => true,
                    'leads_html' => $leads_html,
                    'msg' => __('lang_v1.success')
                ];

                return $output;
            }
        }

        $sources = Category::forDropdown($business_id, 'source');

        $users = User::forDropdown($business_id, false, false, false, true);

        return view('crm::lead.index')
            ->with(compact('sources', 'life_stages', 'lead_view', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module'))) {
            abort(403, 'Unauthorized action.');
        }

        $users = User::forDropdown($business_id, false);
        $sources = Category::forDropdown($business_id, 'source');
        $life_stages = Category::forDropdown($business_id, 'life_stage');

        $types['lead'] = __('crm::lang.lead');
        $store_action = action('\Modules\Crm\Http\Controllers\LeadController@store');

        $module_form_parts = $this->moduleUtil->getModuleData('contact_form_part');

        return view('contact.create')
            ->with(compact('types', 'store_action', 'sources', 'life_stages', 'users', 'module_form_parts'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module'))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['type', 'prefix', 'first_name', 'middle_name', 'last_name','tax_number', 'mobile', 'landline', 'alternate_number', 'city', 'state', 'country', 'landmark', 'contact_id', 'custom_field1', 'custom_field2', 'custom_field3', 'custom_field4', 'custom_field5', 'custom_field6', 'custom_field7', 'custom_field8', 'custom_field9', 'custom_field10', 'email', 'crm_source', 'crm_life_stage', 'dob', 'address_line_1', 'address_line_2', 'zip_code', 'supplier_business_name', 'shipping_custom_field_details']);

            $input['name'] = implode(' ', [$input['prefix'], $input['first_name'], $input['middle_name'], $input['last_name']]);

            if (!empty($request->input('is_export'))) {
                $input['is_export'] = true;
                $input['export_custom_field_1'] = $request->input('export_custom_field_1');
                $input['export_custom_field_2'] = $request->input('export_custom_field_2');
                $input['export_custom_field_3'] = $request->input('export_custom_field_3');
                $input['export_custom_field_4'] = $request->input('export_custom_field_4');
                $input['export_custom_field_5'] = $request->input('export_custom_field_5');
                $input['export_custom_field_6'] = $request->input('export_custom_field_6');
            }

            if (!empty($input['dob'])) {
                $input['dob'] = $this->commonUtil->uf_date($input['dob']);
            }

            $input['business_id'] = $business_id;
            $input['created_by'] = $request->session()->get('user.id');

            $assigned_to = $request->input('user_id');

            $contact = CrmContact::createNewLead($input, $assigned_to);

            if (!empty($contact)) {
                $this->moduleUtil->getModuleData('after_contact_saved', ['contact' => $contact, 'input' => $request->input()]);
            }

            $output = ['success' => true,
                      'msg' => __("contact.added_success")
                  ];

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' =>__("messages.something_went_wrong")
                        ];
        }

        return $output;
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $can_access_all_leads = auth()->user()->can('crm.access_all_leads');
        $can_access_own_leads = auth()->user()->can('crm.access_own_leads');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module')) || !($can_access_all_leads || $can_access_own_leads)) {
            abort(403, 'Unauthorized action.');
        }

        $query = CrmContact::with('leadUsers', 'Source', 'lifeStage')
                    ->where('business_id', $business_id);

        if (!$can_access_all_leads && $can_access_own_leads) {
            $query->where( function($qry) {
                $qry->whereHas('leadUsers', function($q){
                    $q->where('user_id', auth()->user()->id);
                })->orWhere('created_by', auth()->user()->id);
            });
        }
        $contact = $query->findOrFail($id);

        $leads = CrmContact::leadsDropdown($business_id, false);

        $contact_view_tabs = $this->moduleUtil->getModuleData('get_contact_view_tabs');

        return view('crm::lead.show')
            ->with(compact('contact', 'leads', 'contact_view_tabs'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $can_access_all_leads = auth()->user()->can('crm.access_all_leads');
        $can_access_own_leads = auth()->user()->can('crm.access_own_leads');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module')) || !($can_access_all_leads || $can_access_own_leads)) {
            abort(403, 'Unauthorized action.');
        }

        
        $query = CrmContact::with('leadUsers')
                    ->where('business_id', $business_id);

        if (!$can_access_all_leads && $can_access_own_leads) {
            $query->where( function($qry) {
                $qry->whereHas('leadUsers', function($q){
                    $q->where('user_id', auth()->user()->id);
                })->orWhere('created_by', auth()->user()->id);
            });
        }
        $contact = $query->findOrFail($id);

        $users = User::forDropdown($business_id, false);
        $sources = Category::forDropdown($business_id, 'source');
        $life_stages = Category::forDropdown($business_id, 'life_stage');

        $types['lead'] = __('crm::lang.lead');
        $update_action = action('\Modules\Crm\Http\Controllers\LeadController@update', ['lead' => $id]);
        
        return view('contact.edit')
            ->with(compact('contact', 'types', 'update_action', 'sources', 'life_stages', 'users'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        $can_access_all_leads = auth()->user()->can('crm.access_all_leads');
        $can_access_own_leads = auth()->user()->can('crm.access_own_leads');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module')) || !($can_access_all_leads || $can_access_own_leads)) {
            abort(403, 'Unauthorized action.');
        }

        try {
            
            $input = $request->only(['type', 'prefix','first_name', 'middle_name', 'last_name','tax_number', 'mobile', 'landline', 'alternate_number', 'city', 'state', 'country', 'landmark', 'contact_id', 'custom_field1', 'custom_field2', 'custom_field3', 'custom_field4', 'custom_field5', 'custom_field6', 'custom_field7', 'custom_field8', 'custom_field9', 'custom_field10', 'email', 'crm_source', 'crm_life_stage', 'dob', 'address_line_1', 'address_line_2', 'zip_code', 'supplier_business_name', 'shipping_custom_field_details', 'export_custom_field_1', 'export_custom_field_2', 'export_custom_field_3', 'export_custom_field_4', 'export_custom_field_5', 'export_custom_field_6']);

            $input['name'] = implode(' ', [$input['prefix'], $input['first_name'], $input['middle_name'], $input['last_name']]);

            $input['is_export'] = !empty($request->input('is_export')) ? 1 : 0;

            if (!$input['is_export']) {
                unset($input['export_custom_field_1'], $input['export_custom_field_2'], $input['export_custom_field_3'], $input['export_custom_field_4'], $input['export_custom_field_5'], $input['export_custom_field_6']);
            }

            if (!empty($input['dob'])) {
                $input['dob'] = $this->commonUtil->uf_date($input['dob']);
            }

            $assigned_to = $request->input('user_id');

            $contact = CrmContact::updateLead($id, $input, $assigned_to);

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
            
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' =>__("messages.something_went_wrong")
                        ];
        }

        return $output;
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $can_access_all_leads = auth()->user()->can('crm.access_all_leads');
        $can_access_own_leads = auth()->user()->can('crm.access_own_leads');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module')) || !($can_access_all_leads || $can_access_own_leads)) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $query = CrmContact::where('business_id', $business_id);

                if (!$can_access_all_leads && $can_access_own_leads) {
                    $query->where( function($qry) {
                        $qry->whereHas('leadUsers', function($q){
                            $q->where('user_id', auth()->user()->id);
                        })->orWhere('created_by', auth()->user()->id);
                    });
                }
                $contact = $query->findOrFail($id);

                $contact->delete();
                
                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.success')
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }

            return $output;
        }
    }

    public function convertToCustomer($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $contact = CrmContact::where('business_id', $business_id)->findOrFail($id);
                
                $contact->type = 'customer';
                $contact->converted_by = auth()->user()->id;
                $contact->converted_on = \Carbon::now();
                $contact->save();

                $customer = Contact::find($contact->id);

                $this->commonUtil->activityLog($customer, 'converted', null, ['update_note' => __('crm::lang.converted_from_leads')]);
                
                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.success')
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }

            return $output;
        }
    }

    public function postLifeStage($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $contact = CrmContact::where('business_id', $business_id)->findOrFail($id);
                
                $contact->crm_life_stage = request()->input('crm_life_stage');
                $contact->save();
                
                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.success')
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }

            return $output;
        }
    }
}
