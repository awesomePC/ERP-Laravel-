<?php

namespace Modules\Essentials\Http\Controllers;

use App\User;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Essentials\Entities\EssentialsUserShift;
use Modules\Essentials\Entities\Shift;
use Yajra\DataTables\Facades\DataTables;

class ShiftController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ModuleUtil $moduleUtil
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
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if (request()->ajax()) {
            $shifts = Shift::where('essentials_shifts.business_id', $business_id)
                        ->select([
                            'id',
                            'name',
                            'type',
                            'start_time',
                            'end_time',
                            'holidays'
                        ]);

            return Datatables::of($shifts)
                ->editColumn('start_time', function ($row) {
                    $start_time_formated = $this->moduleUtil->format_time($row->start_time);
                    return $start_time_formated ;
                })
                ->editColumn('end_time', function ($row) {
                    $end_time_formated = $this->moduleUtil->format_time($row->end_time);
                    return $end_time_formated ;
                })
                ->editColumn('type', function ($row) {
                    return __('essentials::lang.' . $row->type);
                })
                ->editColumn('holidays', function ($row) {
                    if (!empty($row->holidays)) {
                        $holidays = array_map(function ($item) {
                            return __('lang_v1.' . $item);
                        }, $row->holidays);
                        return implode(', ', $holidays);
                    }
                })
                ->addColumn('action', function ($row) {
                    $html = '<a href="#" data-href="' . action('\Modules\Essentials\Http\Controllers\ShiftController@edit', [$row->id]) . '" data-container="#edit_shift_modal" class="btn-modal btn btn-xs btn-primary"><i class="fas fa-edit" aria-hidden="true"></i> ' . __("messages.edit") . '</a> &nbsp;<a href="#" data-href="' . action('\Modules\Essentials\Http\Controllers\ShiftController@getAssignUsers', [$row->id]) . '" data-container="#user_shift_modal" class="btn-modal btn btn-xs btn-success"><i class="fas fa-users" aria-hidden="true"></i> ' . __("essentials::lang.assign_users") . '</a>';
                    return $html;
                })
                ->removeColumn('id')
                ->rawColumns(['action', 'type'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('essentials::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) && !$is_admin) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['name', 'type', 'holidays']);

            if ($input['type'] != 'flexible_shift') {
                $input['start_time'] = $this->moduleUtil->uf_time($request->input('start_time'));
                $input['end_time'] = $this->moduleUtil->uf_time($request->input('end_time'));
            } else {
                $input['start_time'] = null;
                $input['end_time'] = null;
            }

            $input['is_allowed_auto_clockout'] = !empty($request->input('is_allowed_auto_clockout')) ? 1 : 0;

            if (!empty($request->input('auto_clockout_time'))) {
                $input['auto_clockout_time'] = $this->moduleUtil->uf_time($request->input('auto_clockout_time'));
            }

            $input['business_id'] = $business_id;

            Shift::create($input);

            $output = ['success' => true,
                            'msg' => __("lang_v1.added_success")
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")

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
        return view('essentials::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) && !$is_admin) {
            abort(403, 'Unauthorized action.');
        }
        $shift = Shift::where('business_id', $business_id)
                    ->findOrFail($id);

        $days = $this->moduleUtil->getDays();

        return view('essentials::attendance.shift_modal')->with(compact('shift', 'days'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

            if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) && !$is_admin) {
                abort(403, 'Unauthorized action.');
            }

            $input = $request->only(['name', 'type', 'holidays']);

            if ($input['type'] != 'flexible_shift') {
                $input['start_time'] = $this->moduleUtil->uf_time($request->input('start_time'));
                $input['end_time'] = $this->moduleUtil->uf_time($request->input('end_time'));
            } else {
                $input['start_time'] = null;
                $input['end_time'] = null;
            }

            $input['is_allowed_auto_clockout'] = !empty($request->input('is_allowed_auto_clockout')) ? 1 : 0;

            if (!empty($request->input('auto_clockout_time'))) {
                $input['auto_clockout_time'] = $this->moduleUtil->uf_time($request->input('auto_clockout_time'));
            }

            if (!empty($input['holidays'])) {
                $input['holidays'] = json_encode($input['holidays']);
            } else {
                $input['holidays'] = null;
            }

            $shift = Shift::where('business_id', $business_id)
                        ->where('id', $id)
                        ->update($input);

            $output = ['success' => true,
                                'msg' => __("lang_v1.updated_success")
                            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                
            $output = ['success' => false,
                                'msg' => __("messages.something_went_wrong")

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
        //
    }

    public function getAssignUsers($shift_id)
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) && !$is_admin) {
            abort(403, 'Unauthorized action.');
        }
        $shift = Shift::where('business_id', $business_id)
                    ->with(['user_shifts'])
                    ->findOrFail($shift_id);

        $users = User::forDropdown($business_id, false);

        $user_shifts = [];

        if (!empty($shift->user_shifts)) {
            foreach ($shift->user_shifts as $user_shift) {
                $user_shifts[$user_shift->user_id] = [
                    'start_date' => !empty($user_shift->start_date) ? $this->moduleUtil->format_date($user_shift->start_date) : null,
                    'end_date' => !empty($user_shift->end_date) ? $this->moduleUtil->format_date($user_shift->end_date) : null
                ];
            }
        }

        return view('essentials::attendance.add_shift_users')
                ->with(compact('shift', 'users', 'user_shifts'));
    }

    public function postAssignUsers(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) && !$is_admin) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $shift_id = $request->input('shift_id');
            $shift = Shift::where('business_id', $business_id)
                        ->find($shift_id);

            $user_shifts = $request->input('user_shift');
            $user_shift_data = [];
            $user_ids = [];
            foreach ($user_shifts as $key => $value) {
                if (!empty($value['is_added'])) {
                    $user_ids[] = $key;
                    EssentialsUserShift::updateOrCreate(
                        [
                            'essentials_shift_id' => $shift_id,
                            'user_id' => $key
                        ],
                        [
                            'start_date' => !empty($value['start_date']) ? $this->moduleUtil->uf_date($value['start_date']) : null,
                            'end_date' => !empty($value['end_date']) ? $this->moduleUtil->uf_date($value['end_date']) : null,
                        ]
                    );
                }
            }

            EssentialsUserShift::where('essentials_shift_id', $shift_id)
                            ->whereNotIn('user_id', $user_ids)
                            ->delete();
            
            $output = ['success' => true,
                            'msg' => __("lang_v1.added_success")
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
