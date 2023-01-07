<?php

namespace Modules\Woocommerce\Http\Controllers;

use App\Business;
use App\Transaction;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Woocommerce\Utils\WoocommerceUtil;

class WoocommerceWebhookController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $woocommerceUtil;
    protected $moduleUtil;
    protected $transactionUtil;
    protected $productUtil;

    /**
     * Constructor
     *
     * @param WoocommerceUtil $woocommerceUtil
     * @return void
     */
    public function __construct(WoocommerceUtil $woocommerceUtil, ModuleUtil $moduleUtil, TransactionUtil $transactionUtil, ProductUtil $productUtil)
    {
        $this->woocommerceUtil = $woocommerceUtil;
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
    }

    /**
     * Function to create sale from woocommerce webhook request.
     * @return Response
     */
    public function orderCreated(Request $request, $business_id)
    {
        try {
            $payload = $request->getContent();
            $business = Business::findOrFail($business_id);

            $is_valid_request = $this->isValidWebhookRequest($request, $business->woocommerce_wh_oc_secret);

            if (!$is_valid_request) {
                \Log::emergency("Woocommerce webhook signature mismatch");
            } else {
                $user_id = $business->owner->id;
                $woocommerce_api_settings = $this->woocommerceUtil->get_api_settings($business_id);
                $business_data = [
                    'id' => $business_id,
                    'accounting_method' => $business->accounting_method,
                    'location_id' => $woocommerce_api_settings->location_id,
                    'business' => $business
                ];

                $order_data = json_decode($payload);

                DB::beginTransaction();
                $created = $this->woocommerceUtil->createNewSaleFromOrder($business_id, $user_id, $order_data, $business_data);
                
                $create_error_data = $created !== true ? $created : [];
                $created_data[] = $order_data->number;

                //Create log
                if (!empty($created_data)) {
                    $this->woocommerceUtil->createSyncLog($business_id, $user_id, 'orders', 'created', $created_data, $create_error_data);
                }
                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
        }
    }

    /**
     * Function to update sale from woocommerce webhook request.
     * @return Response
     */
    public function orderUpdated(Request $request, $business_id)
    {
        try {
            $business = Business::findOrFail($business_id);
            $payload = $request->getContent();

            $is_valid_request = $this->isValidWebhookRequest($request, $business->woocommerce_wh_ou_secret);

            if (!$is_valid_request) {
                \Log::emergency("Woocommerce webhook signature mismatch");
            } else {
                $user_id = $business->owner->id;
                $woocommerce_api_settings = $this->woocommerceUtil->get_api_settings($business_id);
                $business_data = [
                    'id' => $business_id,
                    'accounting_method' => $business->accounting_method,
                    'location_id' => $woocommerce_api_settings->location_id
                ];

                $order_data = json_decode($payload);

                $sell = Transaction::where('business_id', $business_id)
                                ->where('woocommerce_order_id', $order_data->id)
                                ->with('sell_lines', 'sell_lines.product', 'payment_lines')
                                ->first();

                if (!empty($sell)) {
                    DB::beginTransaction();

                    $updated = $this->woocommerceUtil->updateSaleFromOrder($business_id, $user_id, $order_data, $sell, $business_data);

                    $updated_data[] = $order_data->number;
                    $update_error_data = $updated !== true ? $updated : [];

                    //Create log
                    if (!empty($updated_data)) {
                        $this->woocommerceUtil->createSyncLog($business_id, $user_id, 'orders', 'updated', $updated_data, $update_error_data);
                    }
                    DB::commit();
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
        }
    }

    /**
     * Function to delete sale from woocommerce webhook request.
     * @return Response
     */
    public function orderDeleted(Request $request, $business_id)
    {
        try {
            $business = Business::findOrFail($business_id);
            $payload = $request->getContent();

            $is_valid_request = $this->isValidWebhookRequest($request, $business->woocommerce_wh_od_secret);

            if (!$is_valid_request) {
                \Log::emergency("Woocommerce webhook signature mismatch");
            } else {
                $user_id = $business->owner->id;
                //$woocommerce_api_settings = $this->woocommerceUtil->get_api_settings($business_id);

                $order_data = json_decode($payload);

                $transaction = Transaction::where('business_id', $business_id)
                                ->where('woocommerce_order_id', $order_data->id)
                                ->with('sell_lines')
                                ->first();

                $log_data[] = $transaction->invoice_no;

                DB::beginTransaction();

                if (!empty($transaction)) {
                    $status_before = $transaction->status;
                    $transaction->status = "draft";
                    $transaction->save();

                    $input['location_id'] = $transaction->location_id;
                    foreach ($transaction->sell_lines as $sell_line) {
                        $input['products']['transaction_sell_lines_id'] = $sell_line->id;
                        $input['products']['product_id'] = $sell_line->product_id;
                        $input['products']['variation_id'] = $sell_line->variation_id;
                        $input['products']['quantity'] = $sell_line->quantity;
                    }

                    //Update product stock
                    $this->productUtil->adjustProductStockForInvoice($status_before, $transaction, $input);

                    $business = ['id' => $business_id,
                                'accounting_method' => $business->accounting_method,
                                'location_id' => $transaction->location_id
                            ];
                    $this->transactionUtil->adjustMappingPurchaseSell($status_before, $transaction, $business);
                }

                //Create log
                if (!empty($log_data)) {
                    $this->woocommerceUtil->createSyncLog($business_id, $user_id, 'orders', 'deleted', $log_data);
                }

                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
        }
    }

    /**
     * Function to restore sale from woocommerce webhook request.
     * @return Response
     */
    public function orderRestored(Request $request, $business_id)
    {
        try {
            $business = Business::findOrFail($business_id);
            $payload = $request->getContent();

            $is_valid_request = $this->isValidWebhookRequest($request, $business->woocommerce_wh_or_secret);

            if (!$is_valid_request) {
                \Log::emergency("Woocommerce webhook signature mismatch");
            } else {
                $user_id = $business->owner->id;
                $woocommerce_api_settings = $this->woocommerceUtil->get_api_settings($business_id);
                $business_data = [
                    'id' => $business_id,
                    'accounting_method' => $business->accounting_method,
                    'location_id' => $woocommerce_api_settings->location_id,
                    'business' => $business
                ];

                $order_data = json_decode($payload);
                $sell = Transaction::where('business_id', $business_id)
                                ->where('woocommerce_order_id', $order_data->id)
                                ->with('sell_lines', 'sell_lines.product', 'payment_lines')
                                ->first();

                DB::beginTransaction();
                //If sell not deleted restore from draft else create new sale
                if (!empty($sell)) {
                    $updated = $this->woocommerceUtil->updateSaleFromOrder($business_id, $user_id, $order_data, $sell, $business_data);

                    $updated_data[] = $order_data->number;
                    $update_error_data = $updated !== true ? $updated : [];

                    //Create log
                    if (!empty($updated_data)) {
                        $this->woocommerceUtil->createSyncLog($business_id, $user_id, 'orders', 'restored', $updated_data, $update_error_data);
                    }
                } else {
                    $created = $this->woocommerceUtil->createNewSaleFromOrder($business_id, $user_id, $order_data, $business_data);
                
                    $create_error_data = $created !== true ? $created : [];
                    $created_data[] = $order_data->number;

                    //Create log
                    if (!empty($created_data)) {
                        $this->woocommerceUtil->createSyncLog($business_id, $user_id, 'orders', 'created', $created_data, $create_error_data);
                    }
                }

                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
        }
    }

    private function isValidWebhookRequest($request, $secret)
    {
        $signature = $request->header('x-wc-webhook-signature');

        $payload = $request->getContent();
        $calculated_hmac = base64_encode(hash_hmac('sha256', $payload, $secret, true));

        if ($signature != $calculated_hmac) {
            return false;
        } else {
            return true;
        }
    }
}
