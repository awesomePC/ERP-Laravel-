<?php

namespace Modules\Crm\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Crm\Entities\Proposal;
use DB;
use Yajra\DataTables\Facades\DataTables;
use App\Utils\ModuleUtil;
use Modules\Crm\Notifications\SendProposalNotification;
use Modules\Crm\Entities\CrmContact;
use Modules\Crm\Entities\ProposalTemplate;
use App\Media;

class ProposalController extends Controller
{   
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {   
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module'))) {
            abort(403, 'Unauthorized action.');
        }
        
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ($request->ajax()) {
            $proposal = Proposal::join('contacts', 'crm_proposals.contact_id', '=', 'contacts.id')
                            ->join('users', 'crm_proposals.sent_by', '=', 'users.id')
                            ->where('crm_proposals.business_id', $business_id)
                            ->select('contacts.name', 'crm_proposals.subject', 'crm_proposals.created_at',
                                'crm_proposals.id', DB::raw("CONCAT(COALESCE(users.surname, ''), ' ', COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')) as sent_by_full_name"));

            if (!$is_admin) {
                $proposal->where('crm_proposals.sent_by', auth()->user()->id);
            }

            return Datatables::of($proposal)
                ->addColumn(
                    'action',
                    function ($row) {
                       $html = '<a href="#" data-href="' . action('\Modules\Crm\Http\Controllers\ProposalController@show', [$row->id]) . '" data-container=".view_modal" class="btn-modal btn-info btn btn-sm">
                            <i class="fa fa-eye" aria-hidden="true"></i> '
                            . __("messages.view") .
                            '</a>';
                        return $html;
                    }
                )
                ->editColumn('created_at', '{{@format_datetime($created_at)}}')
                ->filterColumn('sent_by_full_name', function ($query, $keyword) {
                        $query->whereRaw("CONCAT(COALESCE(users.surname, ''), ' ', COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')) like ?", ["%{$keyword}%"]);
                    })
                ->removeColumn('id')
                ->rawColumns(['action', 'created_at', 'sent_by_full_name'])
                ->make(true);
        }
        
        $proposal_template = ProposalTemplate::with(['media'])
                            ->where('business_id', $business_id)
                            ->first();

        return view('crm::proposal.index')
            ->with(compact('proposal_template'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('crm::create');
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

        $request->validate([
            'contact_id' => 'required',
            'subject' => 'required',
            'body' => 'required'
        ]);

        try {

            $input = $request->only(['subject', 'body', 'contact_id']);
            $input['business_id'] = $business_id;
            $input['sent_by'] = request()->session()->get('user.id');

            DB::beginTransaction();
                $proposal = Proposal::create($input);

                //if template media available make a copy of it for proposal
                $proposal_template = ProposalTemplate::with(['media'])
                                    ->where('business_id', $business_id)
                                    ->first();
                                    
                if ($proposal_template->media->count() > 0) {
                    $file_names = [];
                    foreach ($proposal_template->media as $media) {
                        $doc_name = time().'_'.$media->display_name;
                        $file_names[] = $doc_name;
                        file_put_contents(
                            public_path('uploads/media/').$doc_name, file_get_contents($media->display_url)
                        );
                    }

                    Media::attachMediaToModel($proposal, $business_id, $file_names);
                }

            DB::commit();

            if (!empty($proposal)) {
                $contact = CrmContact::where('business_id', $business_id)
                            ->find($proposal->contact_id);

                $proposal_with_media = Proposal::with(['media'])
                                        ->where('business_id', $business_id)
                                        ->find($proposal->id);

                $contact->notify(new SendProposalNotification($proposal_with_media));
            }

            $output = ['success' => 1,
                        'msg' => __('lang_v1.success')
                    ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                        'msg' => __('messages.something_went_wrong')
                    ];
        }

        return redirect()
            ->action('\Modules\Crm\Http\Controllers\ProposalController@index')
            ->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module'))) {
            abort(403, 'Unauthorized action.');
        }
        
        if (request()->ajax()) {

            $proposal = Proposal::with(['media'])
                        ->join('contacts', 'crm_proposals.contact_id', '=', 'contacts.id')
                        ->join('users', 'crm_proposals.sent_by', '=', 'users.id')
                        ->where('crm_proposals.business_id', $business_id)
                        ->where('crm_proposals.id', $id)
                        ->select('contacts.name as contact', 'crm_proposals.subject as subject', 'crm_proposals.created_at as created_at', DB::raw("CONCAT(COALESCE(users.surname, ''), ' ', COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')) as sent_by_full_name"), 'crm_proposals.body as body', 'crm_proposals.id')
                        ->first();
                        
            return view('crm::proposal.show')
                ->with(compact('proposal'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('crm::edit');
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
