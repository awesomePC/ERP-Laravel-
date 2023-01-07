<?php

namespace Modules\Connector\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Connector\Transformers\CommonResource;
use Modules\Connector\Transformers\BusinessResource;
use App\Account;
use App\Business;
use App\Utils\TransactionUtil;
use App\Utils\BusinessUtil;
use App\Utils\ProductUtil;
use App\Utils\ModuleUtil;
use App\Utils\Util;

/**
 * @authenticated
 *
 */
class CommonResourceController extends ApiController
{

    /**
     * All Utils instance.
     *
     */
    protected $transactionUtil;
    protected $businessUtil;
    protected $productUtil;
    protected $moduleUtil;
    protected $commonUtil;

    public function __construct(TransactionUtil $transactionUtil, BusinessUtil $businessUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, Util $commonUtil)
    {
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->commonUtil = $commonUtil;
    }

    /**
     * List payment accounts
     * @response {
        "data": [
            {
                "id": 1,
                "business_id": 1,
                "name": "Test Account",
                "account_number": "8746888847455",
                "account_type_id": 0,
                "note": null,
                "created_by": 9,
                "is_closed": 0,
                "deleted_at": null,
                "created_at": "2020-06-04 21:34:21",
                "updated_at": "2020-06-04 21:34:21"
            }
        ]
    }
     *
     */
    public function getPaymentAccounts()
    {
        $user = Auth::user();

        $business_id = $user->business_id;
        
        //Accounts
        $accounts = Account::where('business_id', $business_id)
                        ->get();

        return CommonResource::collection($accounts);
    }

    /**
     * List payment methods
     * @response {
        "cash": "Cash",
        "card": "Card",
        "cheque": "Cheque",
        "bank_transfer": "Bank Transfer",
        "other": "Other",
        "custom_pay_1": "Custom Payment 1",
        "custom_pay_2": "Custom Payment 2",
        "custom_pay_3": "Custom Payment 3"
    }
     *
     */
    public function getPaymentMethods()
    {
        $payment_methods = $this->productUtil->payment_types();

        return $payment_methods;
    }

    /**
     * Get business details
     * @response {
        "data": {
            "id": 1,
            "name": "Awesome Shop",
            "currency_id": 2,
            "start_date": "2018-01-01",
            "tax_number_1": "3412569900",
            "tax_label_1": "GSTIN",
            "tax_number_2": null,
            "tax_label_2": null,
            "default_sales_tax": null,
            "default_profit_percent": 25,
            "owner_id": 1,
            "time_zone": "America/Phoenix",
            "fy_start_month": 1,
            "accounting_method": "fifo",
            "default_sales_discount": "10.00",
            "sell_price_tax": "includes",
            "logo": null,
            "sku_prefix": "AS",
            "enable_product_expiry": 0,
            "expiry_type": "add_expiry",
            "on_product_expiry": "keep_selling",
            "stop_selling_before": 0,
            "enable_tooltip": 1,
            "purchase_in_diff_currency": 0,
            "purchase_currency_id": null,
            "p_exchange_rate": "1.000",
            "transaction_edit_days": 30,
            "stock_expiry_alert_days": 30,
            "keyboard_shortcuts": {
                "pos": {
                    "express_checkout": "shift+e",
                    "pay_n_ckeckout": "shift+p",
                    "draft": "shift+d",
                    "cancel": "shift+c",
                    "recent_product_quantity": "f2",
                    "weighing_scale": null,
                    "edit_discount": "shift+i",
                    "edit_order_tax": "shift+t",
                    "add_payment_row": "shift+r",
                    "finalize_payment": "shift+f",
                    "add_new_product": "f4"
                }
            },
            "pos_settings": {
                "amount_rounding_method": null,
                "disable_pay_checkout": 0,
                "disable_draft": 0,
                "disable_express_checkout": 0,
                "hide_product_suggestion": 0,
                "hide_recent_trans": 0,
                "disable_discount": 0,
                "disable_order_tax": 0,
                "is_pos_subtotal_editable": 0
            },
            "weighing_scale_setting": {
                "label_prefix": null,
                "product_sku_length": "4",
                "qty_length": "3",
                "qty_length_decimal": "2"
            },
            "manufacturing_settings": null,
            "essentials_settings": null,
            "ecom_settings": null,
            "woocommerce_wh_oc_secret": null,
            "woocommerce_wh_ou_secret": null,
            "woocommerce_wh_od_secret": null,
            "woocommerce_wh_or_secret": null,
            "enable_brand": 1,
            "enable_category": 1,
            "enable_sub_category": 1,
            "enable_price_tax": 1,
            "enable_purchase_status": 1,
            "enable_lot_number": 0,
            "default_unit": null,
            "enable_sub_units": 0,
            "enable_racks": 0,
            "enable_row": 0,
            "enable_position": 0,
            "enable_editing_product_from_purchase": 1,
            "sales_cmsn_agnt": null,
            "item_addition_method": 1,
            "enable_inline_tax": 1,
            "currency_symbol_placement": "before",
            "enabled_modules": [
                "purchases",
                "add_sale",
                "pos_sale",
                "stock_transfers",
                "stock_adjustment",
                "expenses",
                "account",
                "tables",
                "modifiers",
                "service_staff",
                "booking",
                "kitchen",
                "subscription",
                "types_of_service"
            ],
            "date_format": "m/d/Y",
            "time_format": "24",
            "ref_no_prefixes": {
                "purchase": "PO",
                "purchase_return": null,
                "stock_transfer": "ST",
                "stock_adjustment": "SA",
                "sell_return": "CN",
                "expense": "EP",
                "contacts": "CO",
                "purchase_payment": "PP",
                "sell_payment": "SP",
                "expense_payment": null,
                "business_location": "BL",
                "username": null,
                "subscription": null
            },
            "theme_color": null,
            "created_by": null,
            "enable_rp": 0,
            "rp_name": null,
            "amount_for_unit_rp": "1.0000",
            "min_order_total_for_rp": "1.0000",
            "max_rp_per_order": null,
            "redeem_amount_per_unit_rp": "1.0000",
            "min_order_total_for_redeem": "1.0000",
            "min_redeem_point": null,
            "max_redeem_point": null,
            "rp_expiry_period": null,
            "rp_expiry_type": "year",
            "repair_settings": null,
            "email_settings": {
                "mail_driver": "smtp",
                "mail_host": null,
                "mail_port": null,
                "mail_username": null,
                "mail_password": null,
                "mail_encryption": null,
                "mail_from_address": null,
                "mail_from_name": null
            },
            "sms_settings": {
                "url": null,
                "send_to_param_name": "to",
                "msg_param_name": "text",
                "request_method": "post",
                "param_1": null,
                "param_val_1": null,
                "param_2": null,
                "param_val_2": null,
                "param_3": null,
                "param_val_3": null,
                "param_4": null,
                "param_val_4": null,
                "param_5": null,
                "param_val_5": null,
                "param_6": null,
                "param_val_6": null,
                "param_7": null,
                "param_val_7": null,
                "param_8": null,
                "param_val_8": null,
                "param_9": null,
                "param_val_9": null,
                "param_10": null,
                "param_val_10": null
            },
            "custom_labels": {
                "payments": {
                    "custom_pay_1": null,
                    "custom_pay_2": null,
                    "custom_pay_3": null
                },
                "contact": {
                    "custom_field_1": null,
                    "custom_field_2": null,
                    "custom_field_3": null,
                    "custom_field_4": null
                },
                "product": {
                    "custom_field_1": null,
                    "custom_field_2": null,
                    "custom_field_3": null,
                    "custom_field_4": null
                },
                "location": {
                    "custom_field_1": null,
                    "custom_field_2": null,
                    "custom_field_3": null,
                    "custom_field_4": null
                },
                "user": {
                    "custom_field_1": null,
                    "custom_field_2": null,
                    "custom_field_3": null,
                    "custom_field_4": null
                },
                "purchase": {
                    "custom_field_1": null,
                    "custom_field_2": null,
                    "custom_field_3": null,
                    "custom_field_4": null
                },
                "sell": {
                    "custom_field_1": null,
                    "custom_field_2": null,
                    "custom_field_3": null,
                    "custom_field_4": null
                },
                "types_of_service": {
                    "custom_field_1": null,
                    "custom_field_2": null,
                    "custom_field_3": null,
                    "custom_field_4": null
                }
            },
            "common_settings": {
                "default_datatable_page_entries": "25"
            },
            "is_active": 1,
            "created_at": "2018-01-04 02:15:19",
            "updated_at": "2020-06-04 22:33:01",
            "locations": [
                {
                    "id": 1,
                    "business_id": 1,
                    "location_id": null,
                    "name": "Awesome Shop",
                    "landmark": "Linking Street",
                    "country": "USA",
                    "state": "Arizona",
                    "city": "Phoenix",
                    "zip_code": "85001",
                    "invoice_scheme_id": 1,
                    "invoice_layout_id": 1,
                    "selling_price_group_id": null,
                    "print_receipt_on_invoice": 1,
                    "receipt_printer_type": "browser",
                    "printer_id": null,
                    "mobile": null,
                    "alternate_number": null,
                    "email": null,
                    "website": null,
                    "featured_products": [
                        "5",
                        "71"
                    ],
                    "is_active": 1,
                    "default_payment_accounts": {
                        "cash": {
                            "is_enabled": "1",
                            "account": null
                        },
                        "card": {
                            "is_enabled": "1",
                            "account": null
                        },
                        "cheque": {
                            "is_enabled": "1",
                            "account": null
                        },
                        "bank_transfer": {
                            "is_enabled": "1",
                            "account": null
                        },
                        "other": {
                            "is_enabled": "1",
                            "account": null
                        },
                        "custom_pay_1": {
                            "is_enabled": "1",
                            "account": null
                        },
                        "custom_pay_2": {
                            "is_enabled": "1",
                            "account": null
                        },
                        "custom_pay_3": {
                            "is_enabled": "1",
                            "account": null
                        }
                    },
                    "custom_field1": null,
                    "custom_field2": null,
                    "custom_field3": null,
                    "custom_field4": null,
                    "deleted_at": null,
                    "created_at": "2018-01-04 02:15:20",
                    "updated_at": "2020-06-05 00:56:54"
                }
            ],
            "currency": {
                "id": 2,
                "country": "America",
                "currency": "Dollars",
                "code": "USD",
                "symbol": "$",
                "thousand_separator": ",",
                "decimal_separator": ".",
                "created_at": null,
                "updated_at": null
            },
            "printers": [],
            "currency_precision": 2,
            "quantity_precision": 2
        }
    }
     *
     */
    public function getBusinessDetails()
    {
        $user = Auth::user();

        $business = Business::with(['locations', 'currency', 'printers'])
                        ->findOrFail($user->business_id);

        return new BusinessResource($business);
    }

    /**
     * Get profit and loss report
     * @queryParam location_id optional id of the location Example: 1
     * @queryParam start_date optional format:Y-m-d Example: 2018-06-25
     * @queryParam end_date optional format:Y-m-d Example: 2018-06-25
     * @queryParam user_id optional id of the user Example: 1
     *
     *@response {
        "data": {
            "total_purchase_shipping_charge": 0,
            "total_sell_shipping_charge": 0,
            "total_transfer_shipping_charges": "0.0000",
            "opening_stock": 0,
            "closing_stock": "386859.00000000",
            "total_purchase": 386936,
            "total_purchase_discount": "0.000000000000",
            "total_purchase_return": "0.0000",
            "total_sell": 9764.5,
            "total_sell_discount": "11.550000000000",
            "total_sell_return": "0.0000",
            "total_sell_round_off": "0.0000",
            "total_expense": "0.0000",
            "total_adjustment": "0.0000",
            "total_recovered": "0.0000",
            "total_reward_amount": "0.0000",
            "left_side_module_data": [
                {
                    "value": "0.0000",
                    "label": "Total Payroll",
                    "add_to_net_profit": true
                },
                {
                    "value": 0,
                    "label": "Total Production Cost",
                    "add_to_net_profit": true
                }
            ],
            "right_side_module_data": [],
            "net_profit": 9675.95,
            "gross_profit": -11.55,
            "total_sell_by_subtype": []
        }
    }
     */
    public function getProfitLoss()
    {
        $user = Auth::user();
        $business_id = $user->business_id;
        $fy = $this->businessUtil->getCurrentFinancialYear($business_id);

        $location_id = !empty(request()->input('location_id')) ? request()->input('location_id') : null;
        $start_date = !empty(request()->input('start_date')) ? request()->input('start_date') : $fy['start'];
        $end_date = !empty(request()->input('end_date')) ? request()->input('end_date') : $fy['end'];

        $user_id = request()->input('user_id') ?? null;

        $data = $this->transactionUtil->getProfitLossDetails($business_id, $location_id, $start_date, $end_date, $user_id);

        return [
            'data' => $data
        ];
    }

    /**
     * Get product current stock
     * @response {
        "data": [
            {
                "total_sold": null,
                "total_transfered": null,
                "total_adjusted": null,
                "stock_price": null,
                "stock": null,
                "sku": "AS0001",
                "product": "Men's Reverse Fleece Crew",
                "type": "single",
                "product_id": 1,
                "unit": "Pc(s)",
                "enable_stock": 1,
                "unit_price": "143.0000",
                "product_variation": "DUMMY",
                "variation_name": "DUMMY",
                "location_name": null,
                "location_id": null,
                "variation_id": 1
            },
            {
                "total_sold": "50.0000",
                "total_transfered": null,
                "total_adjusted": null,
                "stock_price": "3850.00000000",
                "stock": "50.0000",
                "sku": "AS0002-1",
                "product": "Levis Men's Slimmy Fit Jeans",
                "type": "variable",
                "product_id": 2,
                "unit": "Pc(s)",
                "enable_stock": 1,
                "unit_price": "77.0000",
                "product_variation": "Waist Size",
                "variation_name": "28",
                "location_name": "Awesome Shop",
                "location_id": 1,
                "variation_id": 2
            },
            {
                "total_sold": "60.0000",
                "total_transfered": null,
                "total_adjusted": null,
                "stock_price": "6930.00000000",
                "stock": "90.0000",
                "sku": "AS0002-2",
                "product": "Levis Men's Slimmy Fit Jeans",
                "type": "variable",
                "product_id": 2,
                "unit": "Pc(s)",
                "enable_stock": 1,
                "unit_price": "77.0000",
                "product_variation": "Waist Size",
                "variation_name": "30",
                "location_name": "Awesome Shop",
                "location_id": 1,
                "variation_id": 3
            }
        ],
        "links": {
            "first": "http://local.pos.com/connector/api/product-stock-report?page=1",
            "last": "http://local.pos.com/connector/api/product-stock-report?page=22",
            "prev": null,
            "next": "http://local.pos.com/connector/api/product-stock-report?page=2"
        },
        "meta": {
            "current_page": 1,
            "from": 1,
            "last_page": 22,
            "path": "http://local.pos.com/connector/api/product-stock-report",
            "per_page": 3,
            "to": 3,
            "total": 66
        }
    }
     */
    public function getProductStock()
    {
        $user = Auth::user();
        $business_id = $user->business_id;

        $filters = request()->only(['location_id', 'category_id', 'sub_category_id',
                                    'brand_id', 'unit_id', 'tax_id', 'type', 
                                    'only_mfg_products', 'active_state', 
                                    'not_for_selling', 'repair_model_id', 
                                    'product_id', 'active_state']);

        $products = $this->productUtil->getProductStockDetails($business_id, $filters, 'api');
        return CommonResource::collection($products);
    }

    /**
     * Get notifications
     * @response {
            "data": [
                {
                    "msg": "Payroll for August/2020 added by Mr. Super Admin. Reference No. 2020/0002",
                    "icon_class": "fas fa-money-bill-alt bg-green",
                    "link": "http://local.pos.com/hrm/payroll",
                    "read_at": null,
                    "created_at": "3 hours ago"
                }
            ]
        }
     */
    public function getNotifications()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->orderBy('created_at', 'DESC')->get();

        $notifications_data = $this->commonUtil->parseNotifications($notifications);

        return new CommonResource($notifications_data);
    }

    /**
     * Get location details from coordinates
     * @bodyParam lat decimal required Lattitude of the location Example: 41.40338
     * @bodyParam lon decimal required Longitude of the location Example: 2.17403
     * @response {
        "address": "Radhanath Mullick Ln, Tiretta Bazaar, Bow Bazaar, Kolkata, West Bengal, 700 073, India"
    }
     *
     */
    public function getLocation()
    {
        $lat = request()->input('lat');
        $lon = request()->input('lon');

        $address = '';
        if (!empty($lat) && !empty($lon)) {
            $address = $this->moduleUtil->getLocationFromCoordinates($lat, $lon);
        }

        return [
            'address' => $address
        ];
    }
}
