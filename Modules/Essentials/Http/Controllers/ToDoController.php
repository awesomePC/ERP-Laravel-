<?php

namespace Modules\Essentials\Http\Controllers;

use App\Media;
use App\User;
use App\Utils\ModuleUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Modules\Essentials\Entities\EssentialsTodoComment;
use Modules\Essentials\Entities\ToDo;
use Modules\Essentials\Notifications\NewTaskCommentNotification;
use Modules\Essentials\Notifications\NewTaskDocumentNotification;
use Modules\Essentials\Notifications\NewTaskNotification;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Activitylog\Models\Activity;

class ToDoController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param CommonUtil
     * @return void
     */
    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil = $moduleUtil;

        $this->priority_colors = [
            'low' => 'bg-green',
            'medium' => 'bg-yellow',
            'high' => 'bg-orange',
            'urgent' => 'bg-red'
        ];

        $this->status_colors = [
            'new' => 'bg-yellow',
            'in_progress' => 'bg-light-blue',
            'on_hold' => 'bg-red',
            'completed' => 'bg-green'
        ];
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        $auth_id = auth()->user()->id;

        $task_statuses = ToDo::getTaskStatus();
        $priorities = ToDo::getTaskPriorities();

        if (request()->ajax()) {
            $todos = ToDo::where('business_id', $business_id)
                        ->with(['users', 'assigned_by'])
                        ->select('*');

            if (!empty($request->priority)) {
                $todos->where('priority', $request->priority);
            }

            if (!empty($request->status)) {
                $todos->where('status', $request->status);
            }

            //If not admin show only assigned task
            if (!$is_admin) {
                $todos->where(function ($query) use ($auth_id) {
                    $query->where('created_by', $auth_id)
                        ->orWhereHas('users', function ($q) use ($auth_id) {
                            $q->where('user_id', $auth_id);
                        });
                });
            }

            //Filter by user id.
            if (!empty($request->user_id)) {
                $user_id = $request->user_id;
                $todos->whereHas('users', function ($q) use ($user_id) {
                    $q->where('user_id', $user_id);
                });
            }

            //Filter by date.
            if (!empty($request->start_date) && !empty($request->end_date)) {
                $start = $request->start_date;
                $end =  $request->end_date;
                $todos->whereDate('date', '>=', $start)
                            ->whereDate('date', '<=', $end);
            }

            return Datatables::of($todos)
                ->addColumn(
                    'action',
                    function ($row) use ($is_admin, $auth_id) {
                        $html = '<div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">'. __("messages.actions") . '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            <li><a href="#" data-href="' . action('\Modules\Essentials\Http\Controllers\ToDoController@edit', [$row->id]) . '" class="btn-modal" data-container="#task_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        
                        if ($is_admin || $row->created_by == $auth_id) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Essentials\Http\Controllers\ToDoController@destroy', [$row->id]) . '" class="delete_task" ><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        }

                        $html .= '<li><a href="' . action('\Modules\Essentials\Http\Controllers\ToDoController@show', [$row->id]) . '" ><i class="fa fa-eye"></i> ' . __("messages.view") . '</a></li>';

                        $html .= '<li><a href="#" class="change_status" data-status="' . $row->status . '" data-task_id="' . $row->id . '"><i class="fas fa-check-circle"></i> ' . __("essentials::lang.change_status") . '</a></li></ul></div>';

                        return $html;
                    }
                )
                ->editColumn('task', function ($row) use ($priorities) {
                    $html = '<a href="' . action('\Modules\Essentials\Http\Controllers\ToDoController@show', [$row->id]) . '" >' . $row->task . '</a> <br>
                        <a data-href="' . action('\Modules\Essentials\Http\Controllers\ToDoController@viewSharedDocs', [$row->id]) . '" class="btn btn-primary btn-xs view-shared-docs">' . __('essentials::lang.docs') . '</a>';

                    if (!empty($row->priority)) {
                        $bg_color = !empty($this->priority_colors[$row->priority]) ? $this->priority_colors[$row->priority] : 'bg-gray';

                        $html .= ' &nbsp; <span class="label ' . $bg_color . '"> ' . $priorities[$row->priority] . '</span>';
                    }
                    return $html;
                })
                ->addColumn('assigned_by', function ($row) {
                    return optional($row->assigned_by)->user_full_name;
                })
                ->editColumn('users', function ($row) {
                    $users = [];
                    foreach ($row->users as $user) {
                        $users[] = $user->user_full_name;
                    }

                    return implode(', ', $users);
                })
                ->editColumn('created_at', '{{@format_datetime($created_at)}}')
                ->editColumn('date', '{{@format_datetime($date)}}')
                ->editColumn('end_date', '@if(!empty($end_date)) {{@format_datetime($end_date)}} @endif')
                ->editColumn('status', function ($row) use ($task_statuses) {
                    $html = '';
                    if (!empty($task_statuses[$row->status])) {
                        $bg_color = !empty($this->status_colors[$row->status]) ? $this->status_colors[$row->status] : 'bg-gray';

                        $html = '<a href="#" class="change_status" data-status="' . $row->status . '" data-task_id="' . $row->id . '"><span class="label ' . $bg_color . '"> ' . $task_statuses[$row->status] . '</span></a>';
                    }
                    return $html;
                })
                ->removeColumn('id')
                ->rawColumns(['task', 'action', 'status'])
                ->make(true);
        }
        
        $users = [];
        if (auth()->user()->can('essentials.assign_todos')) {
            $users = User::forDropdown($business_id, false);
        }

        return view('essentials::todo.index')->with(compact('users', 'task_statuses', 'priorities'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }
        $users = [];
        if (auth()->user()->can('essentials.assign_todos')) {
            $users = User::forDropdown($business_id, false);
        }
        if (!empty(request()->input('from_calendar'))) {
            $users = [];
        }

        $task_statuses = ToDo::getTaskStatus();
        $priorities = ToDo::getTaskPriorities();

        return view('essentials::todo.create')->with(compact('users', 'task_statuses', 'priorities'));
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        $query = ToDo::where('business_id', $business_id)
                    ->with([
                        'assigned_by',
                        'comments',
                        'comments.added_by',
                        'media',
                        'users',
                        'media.uploaded_by_user'
                    ]);

        //If not admin show only assigned task
        if (!$is_admin) {
            $query->where(function ($query) {
                $query->where('created_by', auth()->user()->id)
                    ->orWhereHas('users', function ($q) {
                        $q->where('user_id', auth()->user()->id);
                    });
            });
        }

        $todo = $query->findOrFail($id);

        $users = [];
        foreach ($todo->users as $user) {
            $users[] = $user->user_full_name;
        }
        $task_statuses = ToDo::getTaskStatus();
        $priorities = ToDo::getTaskPriorities();

        $activities = Activity::forSubject($todo)
           ->with(['causer', 'subject'])
           ->latest()
           ->get();

        return view('essentials::todo.view')->with(compact(
            'todo',
            'users',
            'task_statuses',
            'priorities',
            'activities'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        $user_id = auth()->user()->id;
        $query = ToDo::where('business_id', $business_id);
        
        //Non admin can update only assigned tasks
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        if (!$is_admin) {
            $query->where(function ($query) {
                $query->where('created_by', auth()->user()->id)
                    ->orWhereHas('users', function ($q) {
                        $q->where('user_id', auth()->user()->id);
                    });
            });
        }

        $todo = $query->with(['users'])->findOrFail($id);

        $users = [];
        if (auth()->user()->can('essentials.assign_todos')) {
            $users = User::forDropdown($business_id, false);
        }
        $task_statuses = ToDo::getTaskStatus();
        $priorities = ToDo::getTaskPriorities();

        return view('essentials::todo.edit')->with(compact('users', 'todo', 'task_statuses', 'priorities'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }
        
        if (request()->ajax()) {
            try {
                $created_by = $request->session()->get('user.id');
                $input = $request->only(
                    'task',
                    'date',
                    'description',
                    'estimated_hours',
                    'priority',
                    'status',
                    'end_date'
                );
                
                $input['date'] = $this->commonUtil->uf_date($input['date'], true);
                $input['end_date'] = !empty($input['end_date']) ? $this->commonUtil->uf_date($input['end_date'], true) : null;
                $input['business_id'] = $business_id;
                $input['created_by'] = $created_by;
                $input['status'] = !empty($input['status']) ? $input['status'] : 'new';

                $users = $request->input('users');
                //Can add only own tasks if permission not given
                if (!auth()->user()->can('essentials.assign_todos') || empty($users)) {
                    $users = [$created_by];
                }

                $ref_count = $this->commonUtil->setAndGetReferenceCount('essentials_todos');
                //Generate reference number
                $settings = request()->session()->get('business.essentials_settings');
                $settings = !empty($settings) ? json_decode($settings, true) : [];
                $prefix = !empty($settings['essentials_todos_prefix']) ? $settings['essentials_todos_prefix'] : '';
                $input['task_id'] = $this->commonUtil->generateReferenceNumber('essentials_todos', $ref_count, null, $prefix);

                $to_dos = ToDo::create($input);

                $to_dos->users()->sync($users);

                //Exclude created user from notification
                $users = $to_dos->users->filter(function ($item) use ($created_by) {
                    return $item->id != $created_by;
                });

                $this->commonUtil->activityLog($to_dos, 'added');

                \Notification::send($users, new NewTaskNotification($to_dos));
                
                $output = [
                          'success' => true,
                          'msg' => __('lang_v1.success'),
                          'todo_id' => $to_dos->id
                        ];
            } catch (\Exception $e) {
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
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $business_id = $request->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                if (!$request->has('only_status')) {
                    $input = $request->only(
                        'task',
                        'date',
                        'description',
                        'estimated_hours',
                        'priority',
                        'status',
                        'end_date'
                    );
                    
                    $input['date'] = $this->commonUtil->uf_date($input['date'], true);
                    $input['end_date'] = !empty($input['end_date']) ? $this->commonUtil->uf_date($input['end_date'], true) : null;

                    $input['status'] = !empty($input['status']) ? $input['status'] : 'new';
                } else {
                    $input = [ 'status' => !empty($request->input('status')) ? $request->input('status') : null];
                }

                $query = ToDo::where('business_id', $business_id);

                //Non admin can update only assigned tasks
                $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
                if (!$is_admin) {
                    $query->where(function ($query) {
                        $query->where('created_by', auth()->user()->id)
                            ->orWhereHas('users', function ($q) {
                                $q->where('user_id', auth()->user()->id);
                            });
                    });
                }

                $todo = $query->findOrFail($id);

                $todo_before = $todo->replicate();

                $todo->update($input);

                if (auth()->user()->can('essentials.assign_todos') && !$request->has('only_status')) {
                    $users = $request->input('users');
                    $todo->users()->sync($users);
                }

                $this->commonUtil->activityLog($todo, 'edited', $todo_before);
                
                $output = [
                          'success' => true,
                          'msg' => __('lang_v1.success')
                        ];
            } catch (\Exception $e) {
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
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

                $todo = ToDo::where('business_id', $business_id);
                //Can destroy only own created tasks if not admin
                if (!$is_admin) {
                    $todo->where('created_by', auth()->user()->id);
                }
                $todo->where('id', $id)
                    ->delete();

                $output = [
                          'success' => true,
                          'msg' => __('lang_v1.success')
                        ];
            } catch (\Exception $e) {
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
     * Add comment to the task
     * @param  Request $request
     * @return Response
     */
    public function addComment(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $input = $request->only(['task_id', 'comment']);
                $query = ToDo::where('business_id', $business_id)
                            ->with('users');
                $auth_id = auth()->user()->id;

                //Non admin can add comment to only assigned tasks
                $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
                if (!$is_admin) {
                    $query->where(function ($query) {
                        $query->where('created_by', auth()->user()->id)
                            ->orWhereHas('users', function ($q) {
                                $q->where('user_id', auth()->user()->id);
                            });
                    });
                }

                $todo = $query->findOrFail($input['task_id']);

                $input['comment_by'] = $auth_id;

                $comment = EssentialsTodoComment::create($input);

                $comment_html = View::make('essentials::todo.comment')
                                ->with(compact('comment'))
                                ->render();
                $output = [
                          'success' => true,
                          'comment_html' => $comment_html,
                          'msg' => __('lang_v1.success')
                        ];

                //Remove auth user from users collection
                $users = $todo->users->filter(function ($user) use ($auth_id) {
                    return $user->id != $auth_id;
                });

                \Notification::send($users, new NewTaskCommentNotification($comment));
            } catch (\Exception $e) {
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
     * Upload documents for a task
     * @param  Request $request
     * @return Response
     */
    public function uploadDocument(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $task_id = $request->input('task_id');
            $query = ToDo::with('users')->where('business_id', $business_id);
            $auth_id = auth()->user()->id;

            //Non admin can add comment to only assigned tasks
            $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
            if (!$is_admin) {
                $query->where(function ($query) {
                    $query->where('created_by', auth()->user()->id)
                        ->orWhereHas('users', function ($q) {
                            $q->where('user_id', auth()->user()->id);
                        });
                });
            }

            $todo = $query->findOrFail($task_id);

            Media::uploadMedia($todo->business_id, $todo, $request, 'documents');

            //Remove auth user from users collection
            $users = $todo->users->filter(function ($user) use ($auth_id) {
                return $user->id != $auth_id;
            });

            $data = [
                'task_id' => $todo->task_id,
                'uploaded_by' => $auth_id,
                'id' => $todo->id,
                'uploaded_by_user_name' => auth()->user()->user_full_name
            ];

            \Notification::send($users, new NewTaskDocumentNotification($data));

            $output = [
                      'success' => true,
                      'msg' => __('lang_v1.success')
                    ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = [
                        'success' => false,
                        'msg' => __('messages.something_went_wrong')
                        ];
        }

        return back()->with('status', $output);
    }

    /**
     * Delete comment of a task
     * @param  int $id
     * @return Response
     */
    public function deleteComment($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $comment = EssentialsTodoComment::where('comment_by', auth()->user()->id)
                                    ->where('id', $id)
                                    ->delete();
            $output = [
                      'success' => true,
                      'msg' => __('lang_v1.success')
                    ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = [
                        'success' => false,
                        'msg' => __('messages.something_went_wrong')
                        ];
        }

        return $output;
    }

    /**
     * Delete comment of a task
     * @param  int $id
     * @return Response
     */
    public function deleteDocument($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $media = Media::findOrFail($id);
            if ($media->model_type == 'Modules\Essentials\Entities\ToDo') {
                $todo = ToDo::findOrFail($media->model_id);

                //Can delete document only if task is assigned by or assigned to the user
                if (in_array(auth()->user()->id, [$todo->user_id, $todo->created_by])) {
                    unlink($media->display_path);
                    $media->delete();
                }
            }
            $output = [
                      'success' => true,
                      'msg' => __('lang_v1.success')
                    ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = [
                        'success' => false,
                        'msg' => __('messages.something_went_wrong')
                    ];
        }

        return $output;
    }

    public function viewSharedDocs($id)
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            
            $module_data = $this->moduleUtil->getModuleData('getSharedSpreadsheetForGivenData', ['business_id' => $business_id, 'shared_with' => 'todo', 'shared_id' => $id]);
            
            $sheets = [];
            if (!empty($module_data['Spreadsheet'])) {
                $sheets = $module_data['Spreadsheet'];
            }

            $todo = ToDo::findOrFail($id);

            return view('essentials::todo.view_shared_docs')
                ->with(compact('sheets', 'todo'));
        }
    }
}
