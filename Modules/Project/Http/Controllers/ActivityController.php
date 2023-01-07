<?php

namespace Modules\Project\Http\Controllers;

use App\DocumentAndNote;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Modules\Project\Entities\Project;
use Modules\Project\Entities\ProjectTask;
use Modules\Project\Entities\ProjectTimeLog;
use Spatie\Activitylog\Models\Activity;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (request()->ajax()) {
            try {
                $project_id = request()->get('project_id');
                $project = Project::findOrFail($project_id);
                $activities = Activity::forSubject($project)
                                ->orWhere(function ($query) use ($project) {
                                    $query->where('subject_type', (new ProjectTask())->getMorphClass())
                                    ->whereIn('subject_id', $project->tasks()->pluck('id'));
                                })
                                ->orWhere(function ($query) use ($project) {
                                    $query->where('subject_type', (new DocumentAndNote())->getMorphClass())
                                    ->whereIn('subject_id', $project->documentsAndnote()->pluck('id'));
                                })
                                ->orWhere(function ($query) use ($project) {
                                    $query->where('subject_type', (new ProjectTimeLog())->getMorphClass())
                                    ->whereIn('subject_id', $project->timeLogs()->pluck('id'));
                                })
                                ->with(['causer', 'subject'])
                                ->latest()
                                ->simplePaginate(10);
                
                $activities = View::make('project::activity.show')
                                ->with(compact('activities'))
                                ->render();
                        
                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.success'),
                    'activities' =>  $activities
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

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('project::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('project::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('project::edit');
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
    public function destroy()
    {
    }
}
