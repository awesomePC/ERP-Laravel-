<?php

namespace Modules\Connector\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Laravel\Passport\Passport;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use App\Utils\Util;
use Illuminate\Support\Facades\Artisan;

class ClientController extends Controller
{
    public function __construct(Util $util) {
        $this->util = $util;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        $is_demo = (config('app.env') == 'demo');

        $business_id = request()->session()->get('user.business_id');
        $clients = Passport::client()
                    ->leftJoin('users as u', 'oauth_clients.user_id', '=', 'u.id')
                    ->where('u.business_id', $business_id)
                    ->where('password_client', 1)
                    ->select('oauth_clients.*')
                    ->get()
                    ->makeVisible('secret');

        return view('connector::clients.index')->with(compact('clients', 'is_demo'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('connector::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $client = Passport::client()->forceFill([
                'user_id' => auth()->user()->id,
                'name' => $request->input('name'),
                'secret' => Str::random(40),
                'redirect' => 'http://localhost',
                'personal_access_client' => 0,
                'password_client' => 1,
                'revoked' => false,
            ]);

            $client->save();

            $output = ['success' => true,
                            'msg' => __("lang_v1.added_success")
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('connector::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('connector::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }
        
        $business_id = request()->session()->get('user.business_id');
        $clients = Passport::client()
                        ->leftJoin('users as u', 'oauth_clients.user_id', '=', 'u.id')
                        ->where('u.business_id', $business_id)
                        ->where('oauth_clients.id', $id)
                        ->delete();

        $output = ['success' => true,
                            'msg' => __("lang_v1.deleted_success")
                        ];
        return redirect()->back()->with('status', $output);
    }

    public function regenerate(){
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            Artisan::call('passport:install --force');
            Artisan::call('apidoc:generate');

            $output = ['success' => 1,
                    'msg' => __("lang_v1.success")
                ];

        } catch (Exception $e) {
            $output = ['success' => 1,
                    'msg' => $e->getMessage()
                ];
        }
        
        return redirect()->back()->with('status', $output);
    }
}
