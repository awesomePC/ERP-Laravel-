<?php

namespace Modules\Project\Http\Controllers;

use App\Media;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Modules\Project\Entities\ProjectTask;
use Modules\Project\Entities\ProjectTaskComment;
use Modules\Project\Notifications\NewCommentOnTaskNotification;
use Notification;

class TaskCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('project::index');
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
        try {
            $project_task_id = $request->get('project_task_id');
            $input = $request->only('comment');
            $input['commented_by'] = request()->session()->get('user.id');
            $project_task = ProjectTask::findOrFail($project_task_id);
            $project_comment = $project_task->comments()->create($input);

            $business_id = request()->session()->get('user.business_id');
            
            if (!empty($request->get('file_name')[0])) {
                $file_names = explode(',', $request->get('file_name')[0]);
                Media::attachMediaToModel($project_comment, $business_id, $file_names);
            }

            // send notification to task member
            if (!empty($project_comment)) {
                $members = $project_task->members->pluck('id')->toArray();

                //check if user is a commentor then don't notify him
                foreach ($members as $key => $value) {
                    if ($value == $input['commented_by']) {
                        unset($members[$key]);
                    }
                }

                //Used for broadcast notification
                $project_comment['title'] = __('project::lang.new_comment');
                $project_comment['body'] = strip_tags(__(
                    'project::lang.new_comment_on_task_notification',
                    [
                    'commented_by' => $request->user()->user_full_name,
                    'subject' => $project_task->subject,
                    'task_id' => $project_task->task_id
                    ]
                ));
                $project_comment['link'] = action('\Modules\Project\Http\Controllers\ProjectController@show', ['id' => $project_task->project_id]);
                
                if (!empty($members)) {
                    $notifiable_users = User::find($members);
                    Notification::send($notifiable_users, new NewCommentOnTaskNotification($project_task, $project_comment));
                }
            }

            $comments[] = ProjectTaskComment::with('media', 'commentedBy')
                ->findOrFail($project_comment->id);
                            
            //dynamically view task comment
            $comment_html = View::make('project::task.partials.comment')
                ->with(compact('comments'))
                ->render();

            $output = [
                'success' => true,
                'comment_html' =>  $comment_html,
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
        //
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        if (request()->ajax()) {
            try {
                $task_id = request()->get('task_id');

                $comment = ProjectTaskComment::where('project_task_id', $task_id)
                                ->findOrFail($id);

                $comment->delete();
                $comment->media()->delete();

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

    public function postMedia(Request $request)
    {
        try {
            $file = $request->file('file')[0];

            $file_name = Media::uploadFile($file);

            $output = [
                'success' => true,
                'file_name' => $file_name,
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
