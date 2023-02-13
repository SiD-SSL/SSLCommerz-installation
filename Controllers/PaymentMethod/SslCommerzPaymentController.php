<?php

namespace App\Http\Controllers\PaymentMethod;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Http\Services\CheckoutService;
use App\Library\SslCommerz\SslCommerzNotification;
use Illuminate\Support\Facades\Cookie;

class SslCommerzPaymentController extends Controller
{

    private $checkoutService;



    //-------------------------------------------------------------//
    //                      CONSTRUCT METHOD                       //
    //-------------------------------------------------------------//
    public function __construct()
    {
        $this->checkoutService = new CheckoutService;
    }





    //-------------------------------------------------------------//
    //                  EXAMPLE EASY CHECKOUT METHOD               //
    //-------------------------------------------------------------//    
    public function exampleEasyCheckout()
    {
        return view('exampleEasycheckout');
    }






    //-------------------------------------------------------------//
    //              EXAMPLE HOSTED CHECKOUT METHOD                 //
    //-------------------------------------------------------------//    
    public function exampleHostedCheckout()
    {
        return view('exampleHosted');
    }








    //-------------------------------------------------------------//
    //                          INDEX METHOD                       //
    //-------------------------------------------------------------//
    public function index(Request $request)
    {
        $totalShippingCost  = $request->shipping_cost + $request->extra_shipping_cost_amount - $request->shipping_cost_discount_amount;
        $totalAmount        = ($request->subtotal + $request->total_vat_amount + $totalShippingCost) - ($request->total_discount_amount + $request->coupon_discount_amount + $request->point_amount + $request->wallet_amount);


        $post_data = array();
        $post_data['total_amount'] = $totalAmount; # You cant not pay less than 10
        $post_data['currency']     = "BDT";
        $post_data['tran_id']      = uniqid(); // tran_id must be unique

        $request->request->add(['payment_type'   => 'Online']);
        $request->request->add(['payment_method' => 'SSLCOMMERZ']);
        $request->request->add(['payment_tnx_no' => $post_data['tran_id']]);

        if ($request->point_amount > 0 || $request->wallet_amount > 0) {
            $request->request->add(['payment_status' => 'Partial']);
        }else{
            $request->request->add(['payment_status' => 'Pending']);
        }

        # CUSTOMER INFORMATION
        $post_data['cus_name']         = $request->name;
        $post_data['cus_email']        = $request->email;
        $post_data['cus_add1']         = $request->address;
        $post_data['cus_add2']         = "";
        $post_data['cus_city']         = $request->district_id;
        $post_data['cus_state']        = $request->district_id;
        $post_data['cus_postcode']     = $request->zip_code;
        $post_data['cus_country']      = "Bangladesh";
        $post_data['cus_phone']        = $request->phone;
        $post_data['cus_fax']          = "";

        # SHIPMENT INFORMATION
        $shipToDiffAddress = $request->ship_to_different_address ? true : false;

        $post_data['ship_name']        = $shipToDiffAddress ? $request->receiver_name : $request->name;
        $post_data['ship_add1']        = $shipToDiffAddress ? $request->receiver_address : $request->address;
        $post_data['ship_add2']        = "";
        $post_data['ship_city']        = $shipToDiffAddress ? $request->receiver_district_id : $request->district_id;
        $post_data['ship_state']       = $shipToDiffAddress ? $request->receiver_district_id : $request->district_id;
        $post_data['ship_postcode']    = $shipToDiffAddress ? $request->receiver_zip_code : $request->zip_code;
        $post_data['ship_phone']       = $shipToDiffAddress ? $request->receiver_phone : $request->phone;
        $post_data['ship_country']     = "Bangladesh";

        $post_data['shipping_method']  = "No";
        $post_data['product_name']     = "ABC Product";
        $post_data['product_category'] = "ABC Category";
        $post_data['product_profile']  = "Good";

        # OPTIONAL PARAMETERS
        $post_data['value_a'] = "";
        $post_data['value_b'] = "";
        $post_data['value_c'] = "";
        $post_data['value_d'] = "";

        $this->checkoutService->submitOrder($request);

        $sslc = new SslCommerzNotification();
        $payment_options = $sslc->makePayment($post_data, 'hosted');

        if (!is_array($payment_options)) {
            print_r($payment_options);
            $payment_options = array();
        }
    }







    //-------------------------------------------------------------//
    //                     PAY VIA AJAX METHOD                     //
    //-------------------------------------------------------------//
    public function payViaAjax(Request $request)
    {

        # Here you have to receive all the order data to initate the payment.
        # Lets your oder trnsaction informations are saving in a table called "orders"
        # In orders table order uniq identity is "transaction_id","status" field contain status of the transaction, "amount" is the order amount to be paid and "currency" is for storing Site Currency which will be checked with paid currency.

        $totalShippingCost  = $request->shipping_cost + $request->extra_shipping_cost_amount - $request->shipping_cost_discount_amount;
        $totalAmount        = ($request->subtotal + $request->total_vat_amount + $totalShippingCost) - ($request->total_discount_amount + $request->coupon_discount_amount + $request->point_amount + $request->wallet_amount);


        $post_data = array();
        $post_data['total_amount']  = $totalAmount; # You cant not pay less than 10
        $post_data['currency']      = "BDT";
        $post_data['tran_id']       = uniqid(); // tran_id must be unique

        // $request->payment_type = 'Online';
        $request->request->add(['payment_type'   => 'Online']);
        $request->request->add(['payment_method' => 'SSLCOMMERZ']);
        $request->request->add(['payment_tnx_no' => $post_data['tran_id']]);

        if ($request->point_amount > 0 || $request->wallet_amount > 0) {
            $request->request->add(['payment_status' => 'Partial']);
        } else {
            $request->request->add(['payment_status' => 'Pending']);
        }

        # CUSTOMER INFORMATION
        $post_data['cus_name']      = $request->name;
        $post_data['cus_email']     = $request->email;
        $post_data['cus_add1']      = $request->address;
        $post_data['cus_add2']      = "";
        $post_data['cus_city']      = $request->district_id;
        $post_data['cus_state']     = $request->district_id;
        $post_data['cus_postcode']  = $request->zip_code;
        $post_data['cus_country']   = "Bangladesh";
        $post_data['cus_phone']     = $request->phone;
        $post_data['cus_fax']       = "";

        # SHIPMENT INFORMATION
        $shipToDiffAddress = $request->ship_to_different_address ? true : false;

        $post_data['ship_name']         = $shipToDiffAddress ? $request->receiver_name : $request->name;
        $post_data['ship_add1']         = $shipToDiffAddress ? $request->receiver_address : $request->address;
        $post_data['ship_add2']         = "";
        $post_data['ship_city']         = $shipToDiffAddress ? $request->receiver_district_id : $request->district_id;
        $post_data['ship_state']        = $shipToDiffAddress ? $request->receiver_district_id : $request->district_id;
        $post_data['ship_postcode']     = $shipToDiffAddress ? $request->receiver_zip_code : $request->zip_code;
        $post_data['ship_phone']        = $shipToDiffAddress ? $request->receiver_phone : $request->phone;
        $post_data['ship_country']      = "Bangladesh";
        $post_data['shipping_method']   = "No";
        $post_data['product_name']      = "ABC Product";
        $post_data['product_category']  = "ABC Category";
        $post_data['product_profile']   = "Good";

        # OPTIONAL PARAMETERS
        $post_data['value_a'] = "";
        $post_data['value_b'] = "";
        $post_data['value_c'] = "";
        $post_data['value_d'] = "";

        $this->checkoutService->submitOrder($request);

        $sslc = new SslCommerzNotification();
        $payment_options = $sslc->makePayment($post_data, 'hosted');


        if (!is_array($payment_options)) {
            print_r($payment_options);
            $payment_options = array();
        }
    }








    //-------------------------------------------------------------//
    //                       SUCCESS METHOD                        //
    //-------------------------------------------------------------//
    public function success(Request $request)
    {
        
        $tran_id  = $request->input('tran_id');
        $amount   = $request->input('amount');
        $currency = $request->input('currency');
        $sslc     = new SslCommerzNotification();

        $order_details = Http::get(env('BASE_URL').'/api/get-order-by-tnx-no/'.$tran_id)->object();

        if ($order_details->payment_status == 'Pending' || $order_details->payment_status == 'Partial') {
            $validation = $sslc->orderValidate($request->all(), $tran_id, $amount, $currency);

            if ($validation == TRUE) {
                $payment_status = 'Paid';
            } else {
                $payment_status = 'Failed';
            }
        } else if ($order_details->payment_status == 'Processing' || $order_details->payment_status == 'Complete' || $order_details->payment_status == 'Partial') {
            $payment_status = 'Complete';
        } else {
            dd("Invalid Transaction");
        }

        $request->request->add(['payment_tnx_no' => $tran_id]);
        $request->request->add(['payment_status' => $payment_status]);

        Http::post(env('BASE_URL').'/api/update-order-payment-status/', $request->all());

        session()->put('checkoutMessage', 'Order has been Placed Successfully');
        session()->put('isOnlineCheckout', 'true');
        
        return redirect()->route('index');

    }


    public function fail(Request $request)
    {
        $tran_id = $request->input('tran_id');

        $order_detials = DB::table('orders')
            ->where('transaction_id', $tran_id)
            ->select('transaction_id', 'status', 'currency', 'amount')->first();

        if ($order_detials->status == 'Pending') {
            $update_product = DB::table('orders')
                ->where('transaction_id', $tran_id)
                ->update(['status' => 'Failed']);
            echo "Transaction is Falied";
        } else if ($order_detials->status == 'Processing' || $order_detials->status == 'Complete') {
            echo "Transaction is already Successful";
        } else {
            echo "Transaction is Invalid";
        }

    }









    //-------------------------------------------------------------//
    //                        CANCLE METHOD                        //
    //-------------------------------------------------------------//    
    public function cancel(Request $request)
    {
        $tran_id = $request->input('tran_id');

        $order_detials = DB::table('orders')
        ->where('transaction_id', $tran_id)
        ->select('transaction_id', 'status', 'currency', 'amount')->first();

        if ($order_detials->status == 'Pending') {

            $update_product = DB::table('orders')
            ->where('transaction_id', $tran_id)
            ->update(['status' => 'Canceled']);

            echo "Transaction is Cancel";

        } else if ($order_detials->status == 'Processing' || $order_detials->status == 'Complete') {
            echo "Transaction is already Successful";
        } else {
            echo "Transaction is Invalid";
        }


    }








    //-------------------------------------------------------------//
    //                         IPN METHOD                          //
    //-------------------------------------------------------------//      
    public function ipn(Request $request)
    {
        #Received all the payement information from the gateway
        if ($request->input('tran_id')) #Check transation id is posted or not.
        {

            $tran_id = $request->input('tran_id');

            #Check order status in order tabel against the transaction id or order id.
            $order_details = DB::table('orders')
            ->where('transaction_id', $tran_id)
            ->select('transaction_id', 'status', 'currency', 'amount')->first();

            if ($order_details->status == 'Pending') {
                
                $sslc       = new SslCommerzNotification();
                $validation = $sslc->orderValidate($request->all(), $tran_id, $order_details->amount, $order_details->currency);
                
                if ($validation == TRUE) {
                    /*
                    That means IPN worked. Here you need to update order status
                    in order table as Processing or Complete.
                    Here you can also sent sms or email for successful transaction to customer
                    */
                    $update_product = DB::table('orders')
                    ->where('transaction_id', $tran_id)
                    ->update(['status' => 'Processing']);

                    echo "Transaction is successfully Completed";
                } else {
                    /*
                    That means IPN worked, but Transation validation failed.
                    Here you need to update order status as Failed in order table.
                    */
                    $update_product = DB::table('orders')
                        ->where('transaction_id', $tran_id)
                        ->update(['status' => 'Failed']);

                    echo "validation Fail";
                }

            } else if ($order_details->status == 'Processing' || $order_details->status == 'Complete') {

                #That means Order status already updated. No need to udate database.

                echo "Transaction is already successfully Completed";
            } else {
                #That means something wrong happened. You can redirect customer to your product page.

                echo "Invalid Transaction";
            }
        } else {
            echo "Invalid Data";
        }
    }

}
