<?php

namespace App\Http\Controllers;

use App\City;
use App\Order;
use App\Product;
use App\OrderProduct;
use GuzzleHttp\Client;
use App\Mail\OrderPlaced;
use Illuminate\Http\Request;
use App\Mail\OrderPlacedAdmin;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\CheckoutRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CheckoutController extends Controller
{
    public function checkout(CheckoutRequest $request){
        if ($this->productsAreNoLongerAvailable()) {
            return back()->withErrors('Sorry! One of the items in your cart is no longer avialble.');
        }
        $contents = Cart::content()->map(function ($item) {
            return $item->model->name.', '.$item->qty  . ' , ' . $item->model->presentPrice();
        })->values()->toJson();
        if($request->paymentMethod == 'cashOnDelivery'){

            $order = $this->addToOrdersTables($request, null );
            $products = $order->products;
            Mail::send(new OrderPlaced($order));
            Mail::send(new OrderPlacedAdmin($order));
            $this->decreaseQuantities();
            Cart::instance('default')->destroy();
            session()->forget('coupon');
            return view('design.confirmation')->with(['order' => $order , 'products' => $products]);
        }else{
            $preRequest = $request;
            $preRequest->paymentMethod = 'Accept';
            $order = $this->addToOrdersTables($preRequest, null );
            $products = $order->products;
            
            $url =  $this->weAccept($preRequest , $order);
            return redirect($url);
            
            
            // return view('design.braintree')->with(['preRequest' => $preRequest,'token' => $token,]);
            
        }
    }

    public function confirm(Request $request)
    {
        $id = $request->merchant_order_id;
        $success = $request->success;
        $errorcode = (int)$request->txn_response_code;
        $errors = [
    	        'There was an error processing the transaction',
    	        'Contact card issuing bank',
    	        '',
    	        'Expired Card',
    	        'Insufficient Funds',
    	        'Payment is already being processed'
    	    ];
    	 if(in_array($errorcode, range(1,5))){
    	     $errormsg = $errors[$errorcode - 1];
    	 }else{
    	     $errormsg = 'Something went wrong and payment cannot be proceeded. Please check the information you have provided or try contacting your bank.';
    	 }
    
        $error = $request->error_occured;
        $order  = Order::find($id);
        $products = $order->products;
        if($error == "false" && $success == "true"){
             $this->decreaseQuantities();
            Cart::instance('default')->destroy();
            session()->forget('coupon');
           
        }else{
            $order->error = $errormsg;
    	   $order->save();
        }
       
        return view('design.confirmation')->with(['order' => $order , 'products' => $products , 'error' => $error , 'success' => $success, 'errormsg' => $errormsg]);
    }

    public function endpoint(Request $request)
    {
       
        $obj =  $request->obj;
    	$transactionId =$obj['id'];
    	$amountcents = $obj['amount_cents'];
    	$error = $obj['error_occured'];
    	$errorcode = $obj['data']['txn_response_code'];
    	$success = $obj['success'];
    	$order_id = $obj['order']['merchant_order_id'];
    	$order = Order::find($order_id);
    	$errors = [
    	        'There was an error processing the transaction',
    	        'Contact card issuing bank',
    	        'Expired Card',
    	        'Insufficient Funds',
    	        'Payment is already being processed'
    	    ];
    	if($error == false && $success == true){
    	    
            Mail::send(new OrderPlaced($order));
            Mail::send(new OrderPlacedAdmin($order));
            $this->decreaseQuantities();
            Cart::instance('default')->destroy();
            session()->forget('coupon');
    	    
    	}else{
    	  for($i=1;$i <= count($errors) ; $i++)
    	  {
    	        if($errorcode == $i){
    	            $order->error = $errors[$i];
    	            $order->save();
    	        }else{
    	            $order->error = 'Something went wrong and payment cannot be proceeded. Please check the information you have provided or try contacting your bank.';
    	            $order->save();
    	        }
    	  }
    	   
    	   
    	}
    	
    	
    	// $request = json_decode($transactionId, true);
    	//return $order;	
    }

    protected function addToOrdersTables($request, $error)
    {

        
        $shipping = City::getShippingValue($request->city);
        // Insert into orders table
        $order = Order::create([
            'user_id' => auth()->user()->id ,
            'billing_email' => $request->email,
            'billing_fname' => $request->fname,
            'billing_lname' => $request->lname,
            'billing_address' => $this->fullAddress(auth()->user()),
            'billing_city' => $request->city,
            'billing_postalcode' => $request->postalcode,
            'billing_phone' => $request->phone,
            'billing_discount' => $request->discount,
            'billing_discount_code' => $request->coupon,
            'payment_gateway' => $request->paymentMethod,
            'billing_subtotal' => Cart::total(),
            'billing_tax' => Cart::tax(),
            'billing_total' => $request->billing_total * 100,
            'billing_shipping' => $shipping,
            'error' => $error,
        ]);

        // Insert into order_product table
        foreach (Cart::instance('default')->content() as $item) {
            OrderProduct::create([
                'order_id' => $order->id,
                'product_id' => $item->model->id,
                'quantity' => $item->qty,
            ]);
        }

        return $order;
    }

    protected function fullAddress($user)
    {
        
        $address = $user->name .' ' . $user->last_name .'<br> ' . $user->street .' Street <br>'. $user->floor .' '. 'floor Apt#' .' '. $user->apartment .' <br>'. $user->city .' '. 'Egypt' ;
        return $address;
    }
    protected function decreaseQuantities()
    {
        foreach (Cart::content() as $item) {
            $product = Product::find($item->model->id);

            $product->update(['quantity' => $product->quantity - $item->qty]);
        }
    }

    protected function productsAreNoLongerAvailable()
    {
        foreach (Cart::content() as $item) {
            $product = Product::find($item->model->id);
            if ($product->quantity < $item->qty) {
                return true;
            }
        }

        return false;
    }

    
    
    protected function weAccept($request , $order)
    {
        $username = env('ACCEPT_USERNAME');
        $password = env('ACCEPT_PASSWORD');
        $key = env('ACCEPT_KEY');
        $merchant = env('ACCEPT_MERCHANT_ID');
        $payment_integration = env('ACCEPT_PAYMENT_INTEGRATION');
        $myorder_id = $order->id;
        $price = $this->splitter($request->billing_total) * 100;
        $address = $request->address;
        $postal = $request->postalcode;
        $phone = $request->phone;
        $email = $request->email;
        $city = $request->city;
        $fname = $request->fname;
        $lname = $request->lname;
        $apartment = $request->apartment;
        $floor = $request->floor;
        $street = $request->street;
        $building = $request->building;
        $state = $request->state;
        $shippingmethod = "PKG";
        $links = [
            'https://accept.paymobsolutions.com/api/auth/tokens',
            'https://accept.paymobsolutions.com/api/ecommerce/orders',
            'https://accept.paymobsolutions.com/api/acceptance/payment_keys',
            'https://accept.paymobsolutions.com/api/acceptance/iframes'
        ];
        $headers = [
            'Content-Type' => 'application/json',
        ];

        $client = new Client([
            'headers' => $headers
        ]);

        $body = [
            '{
                "username": "'.$username.'",
                "password": "'.$password.'"
            }',
            '{
              "delivery_needed": "false",
              "merchant_id": "'.$merchant.'",
              "merchant_order_id": "'.$myorder_id.'",
              "amount_cents": "'.$price.'",
              "currency": "EGP",
              "items": []
              
            }',

        ];

        $r = $client->request('POST', $links[0], [
            'body' => $body[0]
        ]);
        try {
            $response = $r->getBody()->getContents();
            $response = json_decode($response, true);
            $token = $response['token'];
            
        } catch (HttpException $ex) {
                echo $ex;
        }

        ///step 2//////////
        $data = [
            "token" => $token
        ];
        $r = $client->request('POST', $links[1], [
                'body' => $body[1],
                'query' => $data
            ]);
            // dd('hi');
            try {
                $response = $r->getBody()->getContents();
                
                $response = json_decode($response, true);
                
                $order_id = $response['id'];
                
            } catch (HttpException $ex) {
                echo $ex;
            } 
        ///step 3//////////*/
        $body = '{
      "auth_token": "'.$token.'",
      "amount_cents": "'.$price.'", 
      "expiration": 36000, 
      "order_id": "'.$order_id.'",    
      "billing_data": {
        "apartment": "'.$apartment.'", 
        "email": "'.$email.'", 
        "floor": "'.$floor.'", 
        "first_name": "'.$fname.'", 
        "street": "'.$street.'", 
        "building": "'.$building.'", 
        "phone_number": "'.$phone.'", 
        "shipping_method": "'.$shippingmethod.'", 
        "postal_code": "'.$postal.'", 
        "city": "'.$city.'", 
        "country": "EG", 
        "last_name": "'.$lname.'", 
        "state": "'.$state.'"
      }, 
      "currency": "EGP", 
      "integration_id": '.$payment_integration.',
      "lock_order_when_paid": "false"
    }';

        $r = $client->request('POST', $links[2], [
            'body' => $body,
        ]);
        try {
            $response = $r->getBody()->getContents();
            $response = json_decode($response, true);
            $payment_key = $response['token'];
            $url = 'https://accept.paymobsolutions.com/api/acceptance/iframes/7557?payment_token='.$payment_key;
            // dd($url);
            return  $url;
        } catch (HttpException $ex) {
            echo $ex;
        } 
        
        // //step 4
        // $url = 'https://accept.paymobsolutions.com/api/acceptance/transactions/'.$order_id.'/hmac_calc';
        // $hmac_headers = [
        //     'Content-Type' => 'application/json',
        //     'Authorization' => 'Bearer ' . $token,  
        // ];
        

        // $hmac_client = new Client([
        //     'headers' => $hmac_headers
        // ]);
        // $r = $hmac_client->request('GET', $url);
        // try {
        //     $response = $r->getBody()->getContents();
        //     $response = json_decode($response, true);
        //     $hmac = $response['hmac'];
        //     dd($hmac);
        // } catch (HttpException $ex) {
        //     echo $ex;
        // } 
    }

}
