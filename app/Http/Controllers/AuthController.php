<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Client;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $passwordGrantClient = Client::find(env('PASSPORT_CLIENT_ID', 2));
        // dd($passwordGrantClient);
        $data = [
            'grant_type' => 'password',
            'client_id' => $passwordGrantClient->id,
            'client_secret' => $passwordGrantClient->secret,
            'username' => $request->email,
            'password' => $request->password,
            'scope' => '*',
        ];

        $tokenRequest =  Request::create('oauth/token' , 'post', $data );
        
        
        return app()->handle($tokenRequest);


    }

    public function register(RegisterRequest $request)
    {

        // $user = User::create();
        $user =  DB::table('users')->insert([
            'name'      => $request->name,
            'email'     => $request->email,
            'postal'       => $request->postal,
            'last_name' => $request->last_name,
            'address1'  => $request->address1,
            'address2'  => $request->address2,
            'floor'  => $request->floor,
            'postal'  => $request->postal,
            'state'  => $request->state,
            'building'  => $request->building,
            'street'  => $request->street,
            'apartment'  => $request->apartment,
            'phone'     => $request->phone,
            'city'      => $request->city,
            'password'  => bcrypt($request->password)
        ]);

        if(!$user) return  response()->json(['success' => 'false' , 'message' => 'registration_faild']);
        return response()->json(['success' => 'true' , 'message' => 'registration_success']);
    }
}
