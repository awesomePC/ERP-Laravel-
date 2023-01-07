<?php

namespace Modules\Essentials\Http\Controllers;

use App\User;
use App\Utils\ModuleUtil;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Essentials\Entities\Document;

use Modules\Essentials\Entities\DocumentShare;

use Yajra\DataTables\Facades\DataTables;

class DocumentController extends Controller
{
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
    public function index(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        $type = $request->get('type');
        
        if (request()->ajax()) {
            $user_id = $request->session()->get('user.id');
            $role_id = User::find($user_id)->roles()->first()->id;
            $type = request()->get('type');

            $documents = Document::leftJoin('essentials_document_shares', 'essentials_documents.id', '=', 'essentials_document_shares.document_id')
                ->join('users', 'essentials_documents.user_id', '=', 'users.id')
                ->where('essentials_documents.business_id', $business_id)
                ->where('user_id', $user_id)
                ->having('type', '=', $type)
                ->orWhere(function ($query) use ($role_id) {
                    $query->where('essentials_document_shares.value', '=', $role_id)
                        ->where('essentials_document_shares.value_type', '=', 'role');
                })
                ->orwhere(function ($query) use ($user_id) {
                    $query->where('essentials_document_shares.value', '=', $user_id)
                        ->where('essentials_document_shares.value_type', '=', 'user');
                })
                ->select('users.first_name', 'users.last_name', 'essentials_documents.type', 'essentials_documents.user_id', 'essentials_documents.name', 'essentials_documents.description', 'essentials_documents.created_at', 'essentials_documents.id')
                ->groupBy('essentials_documents.id');
            
            return DataTables::of($documents)
                ->addColumn(
                    'action',
                    '@php
                    $session_userid = request()->session()->get("user.id");
                    @endphp

                    @if($session_userid == $user_id)
                    <button data-href ="{{action(\'\Modules\Essentials\Http\Controllers\DocumentController@destroy\',[$id])}}" class="btn btn-danger btn-xs delete_doc">
                     <i class="fa fa-trash"></i>
                     @lang( "essentials::lang.delete")
                    </button>

                    <button data-href ="{{action(\'\Modules\Essentials\Http\Controllers\DocumentShareController@edit\',[$id])}}" class="btn btn-success btn-xs share_doc">
                         <i class="fa fa-share"></i>
                         @lang( "essentials::lang.share")
                    </button>
                    @endif
                    @if($type == "document")
                        <a href ="{{action(\'\Modules\Essentials\Http\Controllers\DocumentController@download\',[$id])}}" class="btn btn-info btn-xs download">
                             <i class="fa fa-download"></i>
                             @lang( "essentials::lang.download")
                        </a>
                    @elseif($type == "memos")
                            <button data-href ="{{action(\'\Modules\Essentials\Http\Controllers\DocumentController@show\',[$id])}}" class="btn btn-primary btn-xs view_memos">
                                <i class="fa fa-eye"></i>
                                @lang("essentials::lang.view")
                            </button>
                    @endif'
                    )
                ->editColumn(
                    'name',
                    '@php
                        $session_userid = request()->session()->get("user.id");
                        $file = explode("_", $name, 2);
                    @endphp
                    @if($type == "document")
                        {{$file["1"]}} <small class="text-muted"><a href="/uploads/documents/{{$name}}" target="_blank" ><i class="fa fa-external-link"></i></a></small>
                        @if(file_exists(public_path("uploads/documents/" . $name)))
                        <p class="help-block mb-0">
                            <small>
                            <i class="fa fa-file"></i> 
                            {{ humanFilesize(filesize(public_path("uploads/documents/" . $name))) }}
                            </small>
                        </p>
                        @endif
                        @if($session_userid != $user_id)
                         <p class="help-block mb-0">
                           <small>
                            <i class="fa fa-user"></i>
                            @lang( "essentials::lang.shared_by")
                            {{$first_name}} {{$last_name}}
                           </small>
                         </p>
                        @endif
                    @elseif($type == "memos")
                        @if($session_userid != $user_id)
                            {{$name}}
                            <p class="help-block">
                               <small>
                                <i class="fa fa-user"></i>
                                @lang( "essentials::lang.shared_by")
                                {{$first_name}} {{$last_name}}
                               </small>
                            </p>
                        @else
                            {{$name}}
                        @endif
                    @endif'
                    )
                ->editColumn(
                    'created_at',
                    '@if(!empty($created_at))
                        {{@format_date($created_at)}}
                    @endif'
                    )
                ->removeColumn('id')
                ->rawColumns(['name', 'created_at', 'action'])
                ->make(true);
        }
        
        if (!empty($type)) {
            return view('essentials::memos.index');
        } elseif (empty($type)) {
            return view('essentials::document.index');
        }
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
        
        try {
            $user_id = $request->session()->get('user.id');
            $document = $request->only(['name', 'description']);

            if (is_string($document["name"])) {
                $type = "memos";
            } else {
                $type = "document";
            }
            
            if ($type == "document") {
                $name = $this->moduleUtil->uploadFile($request, 'name', 'documents');
                $type = "document";
            } elseif ($type == "memos") {
                $type = "memos";
                $name = $document['name'];
            }

            $doc = [
                    'business_id' => $business_id,
                    'user_id' => $user_id,
                    'type' => $type,
                    'name' => $name,
                    'description' => $document['description'],
                    ];

            Document::create($doc);

            $output = [
                        'success' => true,
                        'msg' => __('lang_v1.success')
                        ];

            if ($type == "document") {
                return redirect()
                ->action('\Modules\Essentials\Http\Controllers\DocumentController@index')
                ->with('status', $output);
            } else {
                return redirect()
                ->action('\Modules\Essentials\Http\Controllers\DocumentController@index', ['type' => 'memos'])
                ->with('status', $output);
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = [
                        'success' => false,
                        'msg' => __('messages.something_went_wrong')
                        ];

            return back()->with('status', $output);
        }
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

        if (request()->ajax()) {
            $memo = Document::where('business_id', $business_id)
                            ->find($id);

            return view('essentials::document.show')
                    ->with(compact('memo'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('essentials::edit');
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
    public function destroy(Request $request, $id)
    {
        $business_id = $request->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $user_id = $request->session()->get('user.id');

                $document = Document::where('business_id', $business_id)
                                    ->find($id);
                
                $document_user_id = $document->user_id;

                if ($user_id == $document_user_id) {
                    if ($document['type'] == "document") {
                        $file_name = $document->name;
                        $path = "documents/" . $file_name;
                        //delete file from a disk
                        Storage::delete($path);
                    }

                    //delete document/memos from database
                    $document->delete();
                }

                $output = [
                        'success' => true,
                        'msg' => __('lang_v1.success')
                    ];

                return $output;
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

                $output = [
                            'success' => false,
                            'msg' => __('messages.something_went_wrong')
                            ];

                return back()->with('status', $output);
            }
        }
    }

    /**
     * Download a document
     * @return Response
     */
    public function download(Request $request, $id)
    {   
        $business_id = $request->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module'))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $user_id = $request->session()->get('user.id');
            $role_id = User::find($user_id)->roles()->first()->id;
            
            $document = Document::where('business_id', $business_id)
                                ->find($id);
            $creator = $document->user_id;

            $document_shares = DocumentShare::where('document_id', $id)
                ->where(function ($query) use ($user_id) {
                    $query->where('essentials_document_shares.value', '=', $user_id)
                    ->where('essentials_document_shares.value_type', '=', 'user');
                })
                ->orWhere(function ($query) use ($role_id) {
                    $query->where('essentials_document_shares.value', '=', $role_id)
                    ->where('essentials_document_shares.value_type', '=', 'role');
                })
                ->first();
            
            $name = $document->name;
            $file = explode('_', $name, 2);
            $file_name = $file['1'];

            $path = "documents/" . $name;

            if ($user_id == $creator || $role_id == $document_shares['value'] || $user_id == $document_shares['value']) {
                return Storage::download($path, $file_name);
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = [
                        'success' => false,
                        'msg' => __('messages.something_went_wrong')
                        ];

            return back()->with('status', $output);
        }
    }
}
