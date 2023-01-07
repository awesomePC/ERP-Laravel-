<?php

namespace Modules\Essentials\Http\Controllers;

use App\BusinessLocation;
use App\User;
use App\Utils\ModuleUtil;


use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Illuminate\Routing\Controller;
use Modules\Essentials\Entities\EssentialsMessage;

use Modules\Essentials\Notifications\NewMessageNotification;

class EssentialsMessageController extends Controller
{
    /**
    * All Utils instance.
    *
    */
    protected $moduleUtil;

    /**
     * Constructor
     *
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

        if (!auth()->user()->can('essentials.view_message') && !auth()->user()->can('essentials.create_message')) {
            abort(403, 'Unauthorized action.');
        }

        $query = EssentialsMessage::where('business_id', $business_id)
                        ->with(['sender'])
                        ->orderBy('created_at', 'ASC');

        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->where(function ($q) use ($permitted_locations) {
                $q->whereIn('location_id', $permitted_locations)
                    ->orWhereRaw('location_id IS NULL');
            });
        }
        $messages = $query->get();

        $business_locations = BusinessLocation::forDropdown($business_id);

        return view('essentials::messages.index')
                ->with(compact('messages', 'business_locations'));
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

        if (!auth()->user()->can('essentials.create_message')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $user_id = $request->session()->get('user.id');

                $input = $request->only(['message', 'location_id']);
                $input['business_id'] = $business_id;
                $input['user_id'] = $user_id;
                $input['message'] = nl2br($input['message']);

                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.success')
                ];

                if (!empty($input['message'])) {
                    //Get last message sent to the same users
                    $last_message = EssentialsMessage::where('location_id', $input['location_id'])
                                                    ->orWhereNull('location_id')
                                                    ->orderBy('created_at', 'desc')
                                                    ->first();

                    $message = EssentialsMessage::create($input);

                    //Check if min 10min passed from last message to the same user
                    $database_notification = empty($last_message) || $last_message->created_at->diffInMinutes(\Carbon::now()) > 10;
                    $this->__notify($message, $database_notification);

                    $output['html'] = view('essentials::messages.message_div', compact('message'))->render();
                }
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

                $output = [
                            'success' => false,
                            'msg' => "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
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
        $business_id = request()->user()->business_id;
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (!auth()->user()->can('essentials.create_message')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $user_id = request()->user()->id;

                EssentialsMessage::where('business_id', $business_id)
                            ->where('user_id', $user_id)
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

    /**
     * Sends notification to the user.
     * @return void
     */
    private function __notify($message, $database_notification = true)
    {
        $business_id = request()->session()->get('user.business_id');
        $query = User::where('id', '!=', $message->user_id)
                    ->where('business_id', $business_id);

        $users = null;
        if (empty($message->location_id)) {
            $users = $query->get();
        } else {
            $users = $query->permission('location.' . $message->location_id)->get();
        }

        if (count($users)) {
            $message->database_notification = $database_notification;
            \Notification::send($users, new NewMessageNotification($message));
        }
    }

    /**
     * Function to get recent messages
     * @return void
     */
    public function getNewMessages()
    {
        $last_chat_time = request()->input('last_chat_time');

        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (!auth()->user()->can('essentials.view_message') && !auth()->user()->can('essentials.create_message')) {
            abort(403, 'Unauthorized action.');
        }

        $query = EssentialsMessage::where('business_id', $business_id)
                        ->where('user_id', '!=', auth()->user()->id)
                        ->with(['sender'])
                        ->orderBy('created_at', 'ASC');

        if (!empty($last_chat_time)) {
            $query->where('created_at', '>', $last_chat_time);
        }

        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->where(function ($q) use ($permitted_locations) {
                $q->whereIn('location_id', $permitted_locations)
                    ->orWhereRaw('location_id IS NULL');
            });
        }
        $messages = $query->get();

        return view('essentials::messages.recent_messages')->with(compact('messages'));
    }
}
