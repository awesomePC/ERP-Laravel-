<?php

namespace Modules\Essentials\Http\Controllers;

use App\User;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Essentials\Entities\Document;
use Modules\Essentials\Entities\DocumentShare;
use Modules\Essentials\Notifications\DocumentShareNotification;

class DocumentShareController extends Controller
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
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }
        
        if (request()->ajax()) {
            $type = request()->get('type');

            $users = User::forDropdown($business_id, false);

            $roles = $this->moduleUtil->getDropdownForRoles($business_id);
            
            $shared_documents = DocumentShare::where('document_id', $id)
                                ->get()
                                ->groupBy('value_type');

            $shared_role = [];
            if (!empty($shared_documents['role'])) {
                $shared_role = $shared_documents['role']->pluck('value')->toArray();
            }

            $shared_user = [];
            if (!empty($shared_documents['user'])) {
                $shared_user = $shared_documents['user']->pluck('value')->toArray();
            }
                        
            return view('essentials::document_share.edit')
                    ->with(compact('users', 'id', 'roles', 'shared_user', 'shared_role', 'type'));
        }
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $document = $request->only(['user', 'role', 'document_id']);
            
            $existing_user_id = [0];
            $existing_role_id = [0];

            $document_obj = Document::find($document['document_id']);

            if (!empty($document['user'])) {
                foreach ($document['user'] as $key => $user_id) {
                    $existing_user_id[] = $user_id;
                    $share = [
                            'document_id' => $document['document_id'],
                            'value_type' => "user",
                            'value' => $user_id,
                        ];
                    $doc_share = DocumentShare::updateOrCreate($share);

                    //Notify document share only if newly created
                    if ($doc_share->wasRecentlyCreated) {
                        $this->notify($document_obj, $user_id);
                    }
                }
            }

            //deleting not existing users
            DocumentShare::where('document_id', $document['document_id'])
                    ->where('value_type', 'user')
                    ->whereNotIn('value', $existing_user_id)
                    ->delete();
            

            if (!empty($document['role'])) {
                foreach ($document['role'] as $key => $role_id) {
                    $existing_role_id[] = $role_id;
                    $share = [
                              'document_id' => $document['document_id'],
                              'value_type' => "role",
                              'value' => $role_id,
                                ];
                                
                    DocumentShare::updateOrCreate($share);
                }
            }

            //deleting not existing roles
            DocumentShare::where('document_id', $document['document_id'])
                       ->where('value_type', 'role')
                       ->whereNotIn('value', $existing_role_id)
                       ->delete();

            $output = [
                        'success' => true,
                        'msg' => __('lang_v1.success')
                        ];

            return $output;
        }
    }

    /**
     * Sends notification to the user.
     * @return void
     */
    private function notify($document, $user_id)
    {
        $user = User::find($user_id);
        $shared_by = auth()->user();

        $user->notify(new DocumentShareNotification($document, $shared_by));
    }
}
