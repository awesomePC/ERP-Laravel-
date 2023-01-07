<?php

namespace Modules\Essentials\Http\Controllers;

use App\BusinessLocation;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Essentials\Entities\EssentialsHoliday;
use Yajra\DataTables\Facades\DataTables;

class EssentialsHolidayController extends Controller
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
            $holidays = EssentialsHoliday::where('essentials_holidays.business_id', $business_id)
                        ->leftJoin('business_locations as bl', 'bl.id', '=', 'essentials_holidays.location_id')
                        ->select([
                            'essentials_holidays.id',
                            'essentials_holidays.name',
                            'bl.name as location',
                            'start_date',
                            'end_date',
                            'note'
                        ]);

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $holidays->where(function ($query) use ($permitted_locations) {
                    $query->whereIn('essentials_holidays.location_id', $permitted_locations)
                        ->orWhereNull('essentials_holidays.location_id');
                });
            }

            if (!empty(request()->input('location_id'))) {
                $holidays->where('essentials_holidays.location_id', request()->input('location_id'));
            }

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $holidays->whereDate('essentials_holidays.start_date', '>=', $start)
                            ->whereDate('essentials_holidays.start_date', '<=', $end);
            }

            return Datatables::of($holidays)
                ->addColumn(
                    'action',
                    function ($row) use ($is_admin) {
                        $html = '';
                        if ($is_admin) {
                            $html .= '<button class="btn btn-xs btn-primary btn-modal" data-container="#add_holiday_modal" data-href="' . action('\Modules\Essentials\Http\Controllers\EssentialsHolidayController@edit', [$row->id]) . '"><i class="fa fa-edit"></i> ' . __("messages.edit") . '</button>
                            &nbsp;
                            <button class="btn btn-xs btn-danger delete-holiday" data-href="' . action('\Modules\Essentials\Http\Controllers\EssentialsHolidayController@destroy', [$row->id]) . '"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</button>
                            ';
                        }

                        return $html;
                    }
                )
                ->editColumn('location', '{{$location ?? __("lang_v1.all")}}')
                ->editColumn('start_date', function ($row) {
                    $start_date = \Carbon::parse($row->start_date);
                    $end_date = \Carbon::parse($row->end_date);

                    $diff = $start_date->diffInDays($end_date);
                    $diff += 1;
                    $start_date_formated = $this->moduleUtil->format_date($start_date);
                    $end_date_formated = $this->moduleUtil->format_date($end_date);
                    return $start_date_formated . ' - ' . $end_date_formated . ' (' . $diff . \Str::plural(__('lang_v1.day'), $diff).')';
                })
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        $locations = BusinessLocation::forDropdown($business_id);

        return view('essentials::holiday.index')->with(compact('locations', 'is_admin'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) && !$is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $locations = BusinessLocation::forDropdown($business_id);

        return view('essentials::holiday.create')->with(compact('locations'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
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
            $input = $request->only(['name', 'start_date', 'end_date', 'location_id', 'note']);

            $input['start_date'] = $this->moduleUtil->uf_date($input['start_date']);
            $input['end_date'] = $this->moduleUtil->uf_date($input['end_date']);
            $input['business_id'] = $business_id;

            EssentialsHoliday::create($input);
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
     * @return Response
     */
    public function show()
    {
        return view('essentials::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) && !$is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $holiday = EssentialsHoliday::where('business_id', $business_id)
                                    ->findOrFail($id);

        $locations = BusinessLocation::forDropdown($business_id);

        return view('essentials::holiday.edit')->with(compact('locations', 'holiday'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $business_id = $request->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) && !$is_admin) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['name', 'start_date', 'end_date', 'location_id', 'note']);

            $input['start_date'] = $this->moduleUtil->uf_date($input['start_date']);
            $input['end_date'] = $this->moduleUtil->uf_date($input['end_date']);

            EssentialsHoliday::where('business_id', $business_id)
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
     * @return Response
     */
    public function destroy($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) && !$is_admin) {
            abort(403, 'Unauthorized action.');
        }

        try {
            EssentialsHoliday::where('business_id', $business_id)
                        ->where('id', $id)
                        ->delete();

            $output = ['success' => true,
                            'msg' => __("lang_v1.deleted_success")
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
