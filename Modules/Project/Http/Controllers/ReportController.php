<?php

namespace Modules\Project\Http\Controllers;

use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Modules\Project\Entities\Project;
use Modules\Project\Entities\ProjectUser;

class ReportController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ModuleUtil
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

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'project_module'))) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('project::reports.index');
    }

    /**
     * Display the time log report
     * by employee
     * @return Response
     */
    public function getEmployeeTimeLogReport(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'project_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            try {
                $user_ids = $request->input('user_id');
                $project_ids = $request->input('project_id');
                $start_date = $request->input('start_date');
                $end_date = $request->input('end_date');

                $query = ProjectUser::with(['projects' => function ($query) use ($project_ids) {
                    //filter by project id
                    if (!empty($project_ids)) {
                        $query->whereIn('pjt_projects.id', $project_ids);
                    }
                },
                'projects.timeLogs' => function ($query) use ($start_date, $end_date) {
                    // filter by start & end date
                    if (!empty($start_date) && !empty($end_date)) {
                        if ($start_date == $end_date) {
                            $query->whereDate('start_datetime', $start_date);
                        } else {
                            $query->whereBetween('start_datetime', [$start_date, $end_date]);
                        }
                    }
                },
                'projects.timeLogs.task'])
                ->where('business_id', $business_id);

                //filter by user
                if (!empty($user_ids)) {
                    $query->whereIn('id', $user_ids);
                }

                $users = $query->get();

                //filter time log for particular user
                foreach ($users as $index => $user) {
                    $is_available_user_timelog = false;
                    foreach ($user->projects as $project) {
                        foreach ($project->timeLogs as $key => $timeLog) {
                            //if timelog user id doesn't match with current user; unset(remove) the timelog
                            if ($timeLog->user_id != $user->id) {
                                unset($project->timeLogs[$key]);
                            } else {
                                $is_available_user_timelog = true;
                            }
                        }
                    }
                    //if user's timelog doesn't exist, remove that user
                    if (!$is_available_user_timelog) {
                        unset($users[$index]);
                    }
                }

                $timelog_report_html = View::make('project::reports.partials.employee_timelog')
                    ->with(compact('users'))
                    ->render();

                $output = [
                    'success' => true,
                    'timelog_report' => $timelog_report_html,
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

        //data for filter
        $employees = ProjectUser::forDropdown($business_id, false);
        $projects = Project::projectDropdown($business_id);

        return view('project::reports.employee_timelog')
            ->with(compact('projects', 'employees'));
    }

    /**
     * Display the time log report
     * by project
     * @return Response
     */
    public function getProjectTimeLogReport(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'project_module'))) {
            abort(403, 'Unauthorized action.');
        }
        
        if ($request->ajax()) {
            try {
                $start_date = $request->input('start_date');
                $end_date = $request->input('end_date');

                $projects = Project::with([
                    'timeLogs' => function ($query) use ($start_date, $end_date) {
                        // filter by start & end date
                        if (!empty($start_date) && !empty($end_date)) {
                            if ($start_date == $end_date) {
                                $query->whereDate('start_datetime', $start_date);
                            } else {
                                $query->whereBetween('start_datetime', [$start_date, $end_date]);
                            }
                        }
                    },
                    'timeLogs.task',
                    'timeLogs.user'])
                ->where('business_id', $business_id);

                //filter by project id
                if (!empty($request->input('project_id'))) {
                    $projects->whereIn('id', $request->input('project_id'));
                }
                
                $projects = $projects->get();

                $timelog_report_html = View::make('project::reports.partials.project_timelog')
                    ->with(compact('projects'))
                    ->render();

                $output = [
                    'success' => true,
                    'timelog_report' => $timelog_report_html,
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

        //data for filters
        $projects = Project::projectDropdown($business_id);

        return view('project::reports.project_timelog')
            ->with(compact('projects'));
    }
}
