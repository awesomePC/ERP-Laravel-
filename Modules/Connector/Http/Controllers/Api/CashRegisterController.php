<?php

namespace Modules\Connector\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\CashRegister;
use Modules\Connector\Transformers\CommonResource;
use Illuminate\Support\Facades\Auth;
use App\CashRegisterTransaction;
use App\Transaction;

/**
 * @group Cash register management
 * @authenticated
 *
 * APIs for managing cash registers
 */
class CashRegisterController extends ApiController
{
    /**
     * List Cash Registers
     * @queryParam status status of the register (open, close) Example: open      
     * @queryParam user_id id of the user Example: 10
     * @queryParam start_date format:Y-m-d Example: 2018-06-25
     * @queryParam end_date format:Y-m-d Example: 2018-06-25
     * @queryParam location_id id of the location Example: 1
     * @queryParam per_page Total records per page. default: 10, Set -1 for no pagination Example:15
     *
     * @response {
        "data": [
            {
                "id": 1,
                "business_id": 1,
                "location_id": 1,
                "user_id": 9,
                "status": "open",
                "closed_at": null,
                "closing_amount": "0.0000",
                "total_card_slips": 0,
                "total_cheques": 0,
                "closing_note": null,
                "created_at": "2020-07-02 12:03:00",
                "updated_at": "2020-07-02 12:03:19",
                "cash_register_transactions": [
                    {
                        "id": 1,
                        "cash_register_id": 1,
                        "amount": "0.0000",
                        "pay_method": "cash",
                        "type": "credit",
                        "transaction_type": "initial",
                        "transaction_id": null,
                        "created_at": "2018-07-13 07:39:34",
                        "updated_at": "2018-07-13 07:39:34"
                    },
                    {
                        "id": 2,
                        "cash_register_id": 1,
                        "amount": "42.5000",
                        "pay_method": "cash",
                        "type": "credit",
                        "transaction_type": "sell",
                        "transaction_id": 41,
                        "created_at": "2018-07-13 07:44:40",
                        "updated_at": "2018-07-13 07:44:40"
                    }
                ]
            },
            {
                "id": 2,
                "business_id": 1,
                "location_id": 1,
                "user_id": 1,
                "status": "",
                "closed_at": "2020-07-02 12:03:00",
                "closing_amount": "0.0000",
                "total_card_slips": 0,
                "total_cheques": 0,
                "closing_note": null,
                "created_at": "2020-07-06 15:38:23",
                "updated_at": "2020-07-06 15:38:23",
                "cash_register_transactions": [
                    {
                        "id": 19,
                        "cash_register_id": 2,
                        "amount": "10.0000",
                        "pay_method": "cash",
                        "type": "credit",
                        "transaction_type": "initial",
                        "transaction_id": null,
                        "created_at": "2020-07-06 15:38:23",
                        "updated_at": "2020-07-06 15:38:23"
                    }
                ]
            }
        ],
        "links": {
            "first": "http://local.pos.com/connector/api/cash-register?page=1",
            "last": null,
            "prev": null,
            "next": null
        },
        "meta": {
            "current_page": 1,
            "from": 1,
            "path": "http://local.pos.com/connector/api/cash-register",
            "per_page": 10,
            "to": 2
        }
    }
     */
    public function index()
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $filters = request()->only(['status', 'user_id', 'location_id', 'start_date', 'end_date', 'per_page']);

        $query = CashRegister::where('business_id', $business_id)
                            ->with(['cash_register_transactions']);

        if (!empty($filters['status']) && in_array($filters['status'], ['open', 'close']) ) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['location_id'])) {
            $query->where('location_id', $filters['location_id']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        $perPage = !empty($filters['per_page']) ? $filters['per_page'] : $this->perPage;
        if ($perPage == -1) {
            $cash_registers = $query->get();
        } else {
            $cash_registers = $query->paginate($perPage);
            $cash_registers->appends(request()->query());
        }

        return CommonResource::collection($cash_registers);
    }

    /**
    * Create Cash Register
    *
    * @bodyParam location_id int required id of the business location Example: 1
    * @bodyParam initial_amount float Initial amount
    * @bodyParam created_at string Register open datetime format:Y-m-d H:i:s, Example: 2020-5-7 15:20:22
    * @bodyParam closed_at string Register closed datetime format:Y-m-d H:i:s, Example: 2020-5-7 15:20:22
    * @bodyParam status register status (open, close) Example:close
    * @bodyParam closing_amount float Closing amount
    * @bodyParam total_card_slips int total number of card slips
    * @bodyParam total_cheques int total number of checks
    * @bodyParam closing_note string Closing note
    * @bodyParam transaction_ids string Comma separated ids of sells associated with the register Example: 1,2,3
    *
    * response {
            "data": {
                "status": "closed",
                "location_id": "1",
                "closed_at": "2020-07-02 12:03:00",
                "business_id": 1,
                "user_id": 1,
                "updated_at": "2020-07-06 16:28:42",
                "created_at": "2020-07-06 16:28:42",
                "id": 3
            }
        }
    */
    public function store(Request $request)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $register_data = $request->only(['status', 'location_id', 
            'created_at', 'closed_at', 'closing_note', 'closing_amount', 'total_card_slips', 'total_cheques']);
        $register_data['business_id'] = $business_id;
        $register_data['user_id'] = $user->id;

        $register = CashRegister::create($register_data);

        $initial_amount = !empty($request->input('initial_amount')) ? $request->input('initial_amount') : 0;
        $cash_register_payments = [];
        if (!empty($initial_amount)) {
            $cash_register_payments[] = new CashRegisterTransaction([
                        'amount' => $initial_amount,
                        'pay_method' => 'cash',
                        'type' => 'credit',
                        'transaction_type' => 'initial'
                    ]);
        }

        $transaction_ids_string = $request->input('transaction_ids');
        $transaction_ids = explode(',', $transaction_ids_string);

        $sells = Transaction::where('business_id', $business_id)
                            ->whereIn('id', $transaction_ids)
                            ->where('status', 'final')
                            ->where('type', 'sell')
                            ->where('created_by', $user->id)
                            ->with(['payment_lines'])
                            ->get();

        foreach ($sells as $sell) {
            foreach ($sell->payment_lines as $payment) {
                $cash_register_payments[] = new CashRegisterTransaction([
                    'amount' => $payment->amount,
                    'pay_method' => $payment->method,
                    'type' => 'credit',
                    'transaction_type' => 'sell',
                    'transaction_id' => $sell->id
                ]);
            }
        }

        if (!empty($cash_register_payments)) {
            $register->cash_register_transactions()->saveMany($cash_register_payments);
        }

        return new CommonResource($register);
    }

    /**
     * Get the specified Register
     * @urlParam cash_register required comma separated ids of the cash registers Example: 59
     *
     * @response {
            "data": [
                {
                    "id": 1,
                    "business_id": 1,
                    "location_id": 1,
                    "user_id": 9,
                    "status": "open",
                    "closed_at": null,
                    "closing_amount": "0.0000",
                    "total_card_slips": 0,
                    "total_cheques": 0,
                    "closing_note": null,
                    "created_at": "2020-07-02 12:03:00",
                    "updated_at": "2020-07-02 12:03:19",
                    "cash_register_transactions": [
                        {
                            "id": 1,
                            "cash_register_id": 1,
                            "amount": "0.0000",
                            "pay_method": "cash",
                            "type": "credit",
                            "transaction_type": "initial",
                            "transaction_id": null,
                            "created_at": "2018-07-13 07:39:34",
                            "updated_at": "2018-07-13 07:39:34"
                        },
                        {
                            "id": 2,
                            "cash_register_id": 1,
                            "amount": "42.5000",
                            "pay_method": "cash",
                            "type": "credit",
                            "transaction_type": "sell",
                            "transaction_id": 41,
                            "created_at": "2018-07-13 07:44:40",
                            "updated_at": "2018-07-13 07:44:40"
                        }
                    ]
                }
            ]
        }
    */
    public function show($register_ids)
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $register_ids = explode(',', $register_ids);
        $cash_registers = CashRegister::where('business_id', $business_id)
                            ->whereIn('id', $register_ids)
                            ->with(['cash_register_transactions'])
                            ->get();

        return CommonResource::collection($cash_registers);
    }
}
