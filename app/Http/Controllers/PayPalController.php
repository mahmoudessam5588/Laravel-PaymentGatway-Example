<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Models\Order;
use Srmklive\PayPal\Facades\PayPal;
use Illuminate\Support\Facades\DB;

class PayPalController extends Controller

{

    public function create(Request $request)
    {

        //the value we are sending
        $data = json_decode($request->getContent(), true);
        //intialize paypal service
        $provider = \PayPal::setProvider();
        $provider->setApiCredentials(config('paypal'));
        $token  = $provider->getAccessToken();
        $provider->setAccessToken($token);
        //creating a model to delegate some of the functionality 
        //other than creating a Giant Controller
        $price = Order::getProductPrice($data['value']);
        $description = Order::getProductDescripton($data['value']);

        //save created order to database 
        $order = $provider->createOrder([
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "curreny_code" => "USD",
                        "value" => $price,
                    ],
                    "description" => $description,
                ]
            ]
        ]);
        //save created order to data 
        Order::create([
            "price" => $price,
            "description" => $description,
            "status" => $order["status"],
            "reference_number" => $order["id"],
        ]);
        return response()->json($order);
    }
    public function capture(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $orderId = $data['orderId'];
        $provider = \PayPal::setProvider();
        $provider->setApiCredentials(config('paypal'));
        $token  = $provider->getAccessToken();
        $provider->setAccessToken($token);
        $result = $provider->capturePaymentOrder($orderId);

        //Update Database
        if ($result['status'] == 'Completed') {
            DB::table('orders')
                ->where('refernce_number', $result['id'])
                ->update(['status' => 'COMPLETED', 'updated_at' => \Carbon\Carbon::now()]);
        }

        return response()->json($result);
    }
}
