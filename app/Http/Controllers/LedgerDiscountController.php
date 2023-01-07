<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;
use App\Utils\Util;
use App\Contact;

class LedgerDiscountController extends Controller
{
    protected $commonUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(
        Util $commonUtil
    ) {
        $this->commonUtil = $commonUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $business_id = $request->session()->get('user.business_id');
            $input = $request->only(['date', 'amount', 'note', 'contact_id']);

            $contact = Contact::find($input['contact_id']);

            $sub_type = 'sell_discount';
            if ($contact->type == 'customer') {
                $sub_type = 'sell_discount';
            } else if ($contact->type == 'supplier') {
                $sub_type = 'purchase_discount';
            } else {
                $sub_type = $request->input('sub_type');
            }
            $transaction_data = [
                'business_id' => $business_id,
                'final_total' => $this->commonUtil->num_uf($input['amount']),
                'total_before_tax' => $this->commonUtil->num_uf($input['amount']),
                'status' => 'final',
                'type' => 'ledger_discount',
                'sub_type' => $sub_type,
                'contact_id' => $input['contact_id'],
                'created_by' => auth()->user()->id,
                'additional_notes' => $input['note'],
                'transaction_date' => $this->commonUtil->uf_date($input['date'], true)
            ];

            $discount = Transaction::create($transaction_data);
            
            $output = ['success' => true, 'msg' => __('lang_v1.success')];

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __('messages.something_went_wrong')
                        ];
        }

        return $output;  
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $is_admin = $this->commonUtil->is_admin(auth()->user());

        if (!$is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        
        $discount = Transaction::where('business_id', $business_id)
                    ->where('type', 'ledger_discount')
                    ->find($id);

        $contact = Contact::find($discount->contact_id);

        return view('ledger_discount.edit')->with(compact('discount', 'contact'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $business_id = $request->session()->get('user.business_id');
            $input = $request->only(['date', 'amount', 'note', 'contact_id']);

            $transaction_data = [
                'final_total' => $this->commonUtil->num_uf($input['amount']),
                'total_before_tax' => $this->commonUtil->num_uf($input['amount']),
                'additional_notes' => $input['note'],
                'transaction_date' => $this->commonUtil->uf_date($input['date'], true)
            ];

            if ($request->has('sub_type')) {
                $transaction_data['sub_type'] = $request->input('sub_type');
            }

            Transaction::where('business_id', $business_id)
                    ->where('type', 'ledger_discount')
                    ->where('id', $id)
                    ->update($transaction_data);
            
            $output = ['success' => true, 'msg' => __('lang_v1.success')];

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __('messages.something_went_wrong')
                        ];
        }

        return $output;  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $is_admin = $this->commonUtil->is_admin(auth()->user());

        if (!$is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        
        try {
            Transaction::where('business_id', $business_id)
                    ->where('type', 'ledger_discount')
                    ->where('id', $id)
                    ->delete();
            
            $output = ['success' => true, 'msg' => __('lang_v1.success')];

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
                        ];
        }

        return $output;
    }
}
