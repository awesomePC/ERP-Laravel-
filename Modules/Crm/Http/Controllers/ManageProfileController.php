<?php


namespace Modules\Crm\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Media;
use App\User;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ManageProfileController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;
    /**
     * Constructor
     *
     * @param CommonUtil
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Shows profile of logged in user
     *
     * @return \Illuminate\Http\Response
     */
    public function getProfile()
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module'))) {
            abort(403, 'Unauthorized action.');
        }

        $user_id = request()->session()->get('user.id');
        $user = User::where('id', $user_id)->with(['media'])->first();
        $config_languages = config('constants.langs');
        $languages = [];
        foreach ($config_languages as $key => $value) {
            $languages[$key] = $value['full_name'];
        }

        return view('crm::profile.edit', compact('user', 'languages'));
    }

    /**
     * updates user profile
     *
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module'))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $user_id = $request->session()->get('user.id');
            $input = $request->only(['surname', 'first_name', 'last_name', 'email', 'language']);

            $user = User::find($user_id);
            $user->update($input);

            Media::uploadMedia($user->business_id, $user, request(), 'profile_photo', true);

            //update session
            $input['id'] = $user_id;
            $input['business_id'] = $business_id;
            session()->put('user', $input);

            $output = ['success' => 1,
                        'msg' => __('lang_v1.profile_updated_successfully')
                    ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                        'msg' => __('messages.something_went_wrong')
                    ];
        }

        return redirect()->back()->with(['status' => $output]);
    }
    /**
     * Update the password
     *
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module'))) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            $user_id = $request->session()->get('user.id');
            $user = User::where('id', $user_id)->first();
            
            if (Hash::check($request->input('current_password'), $user->password)) {
                $user->password = Hash::make($request->input('new_password'));
                $user->save();
                $output = ['success' => 1,
                            'msg' =>  __('lang_v1.password_updated_successfully')
                        ];
            } else {
                $output = ['success' => 0,
                            'msg' => __('lang_v1.u_have_entered_wrong_password')
                        ];
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __('messages.something_went_wrong')
                        ];
        }
        return redirect()->back()->with(['status' => $output]);
    }
}
