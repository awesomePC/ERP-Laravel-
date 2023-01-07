<?php

namespace Modules\Crm\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Modules\Crm\Entities\CrmContact;
use Modules\Crm\Utils\CrmUtil;

class ContactLoginController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;
    protected $crmUtil;
    /**
     * Constructor
     *
     * @param CommonUtil
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil, CrmUtil $crmUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->crmUtil = $crmUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module')) || !auth()->user()->can('crm.access_contact_login')) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            
            $query = User::with('contact')
                    ->where('business_id', $business_id)
                    ->whereHas('contact', function($q){
                        $q->whereNotNull('id');
                    });
                    
            if (!empty($request->get('contact_id'))) {
                $query->where('crm_contact_id', $request->get('contact_id'));
            }

            if (!empty($request->get('crm_contact_id'))) {
                $query->where('crm_contact_id', $request->get('crm_contact_id'));
            }

            $users = $query->select('username', 'email', 'id', 'crm_contact_id', 'surname', 'first_name', 'last_name', 'crm_department', 'crm_designation');

            return Datatables::of($users)
                    ->addColumn('action', function ($row) {
                        $html = '<div class="btn-group">
                                    <button class="btn btn-info dropdown-toggle btn-xs" type="button"  data-toggle="dropdown" aria-expanded="false">
                                        '.__("messages.action").'
                                        <span class="caret"></span>
                                        <span class="sr-only">
                                        '.__("messages.action").'
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-left" role="menu">
                                        <li>
                                            <a data-href="' . action('\Modules\Crm\Http\Controllers\ContactLoginController@edit', ['id' => $row->id, 'crm_contact_id' => $row->crm_contact_id]) . '" class="cursor-pointer edit_contact_login">
                                                <i class="fa fa-edit"></i>
                                                '.__("messages.edit").'
                                            </a>
                                        </li>
                                        <li>
                                            <a data-href="' . action('\Modules\Crm\Http\Controllers\ContactLoginController@destroy', ['id' => $row->id, 'crm_contact_id' => $row->crm_contact_id]) . '"  id="delete_contact_login" class="cursor-pointer">
                                                <i class="fas fa-trash"></i>
                                                '.__("messages.delete").'
                                            </a>
                                        </li>
                                    </ul>
                                </div>';

                        return $html;
                    })
                ->editColumn('name', function ($row) {
                    return $row->surname. ' ' .$row->first_name.' '.$row->last_name;
                })
                ->editColumn('contact', function($row) {
                    return $row['contact']->prefix. ' ' .$row['contact']->first_name.' '.$row['contact']->last_name;  
                })
                ->removeColumn('id')
                ->rawColumns(['action', 'contact', 'name'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module')) || !auth()->user()->can('crm.access_contact_login')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $crm_contact_id = request()->get('contact_id');
            $crud_type = request()->get('crud_type');
            $contacts = [];
            if (!empty($crud_type)) {
                $contacts = CrmContact::contactsDropdownForLogin($business_id);
            }

            return view('crm::contact_login.create')
                ->with(compact('crm_contact_id', 'contacts'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module')) || !auth()->user()->can('crm.access_contact_login')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only('crm_contact_id', 'surname', 'first_name', 'last_name', 'email', 'username', 'password', 'contact_number', 'alt_number', 'family_number', 'crm_department', 'crm_designation');

            $input['status'] = !empty($request->input('is_active')) ? 'active' : 'inactive';
            $input['business_id'] = $business_id;

            $input['allow_login'] = 1;

            $user = $this->crmUtil->creatContactPerson($input);

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module')) || !auth()->user()->can('crm.access_contact_login')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $crm_contact_id = request()->get('crm_contact_id');
            $crud_type = request()->get('crud_type');

            $contacts = [];
            if (!empty($crud_type)) {
                $contacts = CrmContact::contactsDropdownForLogin($business_id);
            }

            $user = User::where('business_id', $business_id)
                        ->where('crm_contact_id', $crm_contact_id)
                        ->findOrFail($id);

            return view('crm::contact_login.edit')
                ->with(compact('user', 'contacts'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module')) || !auth()->user()->can('crm.access_contact_login')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only('surname', 'first_name', 'last_name', 'email', 'username', 'contact_number', 'alt_number', 'family_number', 'crm_department', 'crm_designation');

            $input['status'] = !empty($request->input('is_active')) ? 'active' : 'inactive';

            if (!empty($request->input('password'))) {
                $input['password'] = Hash::make($request->input('password'));
            }

            $input['crm_contact_id'] = $request->get('crm_contact_id');

            $user = User::where('business_id', $business_id)
                        ->where('id', $id)
                        ->update($input);

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module')) || !auth()->user()->can('crm.access_contact_login')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $crm_contact_id = request()->get('crm_contact_id');

                $user = User::where('business_id', $business_id)
                            ->where('crm_contact_id', $crm_contact_id)
                            ->findOrFail($id);

                $user->delete();

                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.success')
                ];
            } catch (Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];
            }
            return $output;
        }
    }

    public function allContactsLoginList()
    { 
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module')) || !auth()->user()->can('crm.access_contact_login')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $contacts = CrmContact::contactsDropdownForLogin($business_id, true);
        return view('crm::contact_login.all_contacts_login')
            ->with(compact('contacts'));
    }
}
