<?php

namespace Modules\Essentials\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Essentials\Entities\KnowledgeBase;
use App\Utils\ModuleUtil;
use App\User;

class KnowledgeBaseController extends Controller
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

        $user_id = auth()->user()->id;
        $knowledge_bases = KnowledgeBase::where('business_id', $business_id)
                                    ->where('kb_type', 'knowledge_base')
                                    ->whereNull('parent_id')
                                    ->with(['children', 'children.children'])
                                    ->where( function($query) use($user_id){
                                        $query->whereHas('users', function($q) use($user_id){
                                            $q->where('user_id', $user_id);
                                        })->orWhere('created_by', $user_id)
                                        ->orWhere('share_with', 'public');
                                    })
                                    ->get();

        return view('essentials::knowledge_base.index')->with(compact('knowledge_bases'));
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

        $parent = null;
        $users = null;
        if (!empty(request()->input('parent'))) {
            $parent = KnowledgeBase::where('business_id', $business_id)
                                ->findOrFail(request()->input('parent'));

        } else {
            $users =  User::forDropdown($business_id, false);
        }

        return view('essentials::knowledge_base.create')
                    ->with(compact('parent', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            $user_id = $request->session()->get('user.id');
            $input = $request->only(['title', 'content']);

            $input['business_id'] = $business_id;
            $input['created_by'] = $user_id;
            $input['kb_type'] = !empty($request->input('kb_type')) ? $request->input('kb_type') : 'knowledge_base';
            $input['parent_id'] = !empty($request->input('parent_id')) ? $request->input('parent_id') : null;
            $input['share_with'] = !empty($request->input('share_with')) ? $request->input('share_with') : null;

            $kb = KnowledgeBase::create($input);

            if ($kb->kb_type == 'knowledge_base' && $kb->share_with == 'only_with') {
                $kb->users()->sync($request->input('user_ids'));
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

        return redirect()->action('\Modules\Essentials\Http\Controllers\KnowledgeBaseController@index')->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        $kb_object = KnowledgeBase::where('business_id', $business_id)
                                    ->with(['children', 'children.children', 'users'])
                                    ->find($id);
        $users = [];

        if (count($kb_object->users) > 0) {
            foreach ($kb_object->users as $user) {
                $users[] = $user->user_full_name;
            }
        }
        $section_id = '';
        $article_id = '';
        if ($kb_object->kb_type == 'knowledge_base') {
            $knowledge_base = $kb_object;
        } else if ($kb_object->kb_type == 'section') {
            $knowledge_base = KnowledgeBase::where('business_id', $business_id)
                                    ->with(['children', 'children.children'])
                                    ->find($kb_object->parent_id);
            $section_id = $kb_object->id;
        } else if ($kb_object->kb_type == 'article') {
            $section = KnowledgeBase::where('business_id', $business_id)
                                    ->find($kb_object->parent_id);

            $section_id = $section->id; 
            $article_id = $kb_object->id;
            $knowledge_base = KnowledgeBase::where('business_id', $business_id)
                                    ->with(['children', 'children.children'])
                                    ->find($section->parent_id);
        }

        return view('essentials::knowledge_base.show')->with(compact('kb_object', 'knowledge_base', 'section_id', 'article_id', 'users'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        $kb = KnowledgeBase::where('business_id', $business_id)
                            ->with(['users'])
                            ->findOrFail($id);

        $users = [];

        if ($kb->kb_type == 'knowledge_base') {
            $users =  User::forDropdown($business_id, false);
        }

        return view('essentials::knowledge_base.edit')->with(compact('kb', 'users'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $business_id = $request->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            $input = $request->only(['title', 'content']);

            $kb = KnowledgeBase::where('business_id', $business_id)->findOrFail($id);

            $input['share_with'] = !empty($request->input('share_with')) ? $request->input('share_with') : null;

            $kb->update($input);

            $user_ids = !empty($request->input('user_ids')) ? $request->input('user_ids') : [];

            if ($kb->kb_type == 'knowledge_base' && $kb->share_with == 'only_with') {
                $kb->users()->sync($user_ids);
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

        return redirect()->action('\Modules\Essentials\Http\Controllers\KnowledgeBaseController@index')->with('status', $output);  
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy(Request $request,$id)
    {
        $business_id = $request->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {

                KnowledgeBase::where('business_id', $business_id)
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
    }
}
