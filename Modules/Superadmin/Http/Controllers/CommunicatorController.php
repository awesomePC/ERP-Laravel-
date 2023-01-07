<?php

namespace Modules\Superadmin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Business;
use App\User;

use Modules\Superadmin\Notifications\SuperadminCommunicator;
use Modules\Superadmin\Entities\SuperadminCommunicatorLog;

use Yajra\DataTables\Facades\DataTables;

class CommunicatorController extends BaseController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        $businesses = Business::orderby('name')
                                ->pluck('name', 'id');

        return view('superadmin::communicator.index')
                ->with(compact('businesses'));
    }

    /**
     * Sends notification to the required business owners.
     * @param  Request $request
     * @return Response
     */
    public function send(Request $request)
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        //Disable in demo
        if (config('app.env') == 'demo') {
            $output = ['success' => 0,
                            'msg' => 'Feature disabled in demo!!'
                        ];
            return back()->with('status', $output);
        }
        
        $input = $request->input();

        //Get business owners
        $business_owners = User::join('business as B', 'users.id', '=', 'B.owner_id')
                        ->whereIn('B.id', $input['recipients'])
                        ->select('users.*')
                        ->groupBy('users.id')
                        ->get();

        //Send notifications
        \Notification::send($business_owners, new SuperadminCommunicator($input));

        //Create Log
        SuperadminCommunicatorLog::create([
            'business_ids' => $input['recipients'],
            'subject' => $input['subject'],
            'message' => $input['message']
        ]);

        $output = ['success' => 1,
                    'msg' => __('lang_v1.success')
                ];
                
        return back()->with('status', $output);
    }

    public function getHistory()
    {
        $history = SuperadminCommunicatorLog::select('subject', 'message', 'created_at');

        return Datatables::of($history)
                         ->editColumn(
                             'created_at',
                             '{{@format_date($created_at)}} {{@format_time($created_at)}}'
                         )
                         ->rawColumns([1])
                         ->make(false);
    }
}
