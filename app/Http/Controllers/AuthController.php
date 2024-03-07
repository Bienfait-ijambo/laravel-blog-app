<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Auth;
use DB;
use Validator;

class AuthController extends Controller
{
    private $secretKey='X.XSJDLKStQBDyw1nIePtYNn3988983t3yBeYCnvG8OxzZ9989I8u+Rq0r0=';

   
     public function register(Request $request) {

        $fields=$request->all();

        $errors = Validator::make($fields, [
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string'
        ]);

        if($errors->fails()) {
            return response($errors->errors()->all(),422);
        }

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        $token = $user->createToken($this->secretKey)->plainTextToken;

        $response = [
            'user'    => $user,
            'message' => 'your account was created successfully !',
            'token'   => $token
        ];

        return response($response, 201);
    }


   


    public function login(Request $request) 
    {

        $fields=$request->all();

        $errors = Validator::make($fields, [
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        if($errors->fails()) {
             return response($errors->errors()->all(),422);
        }

        // Check email
        $user = User::where('email', $fields['email'])->first();

        // Check password
        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Password or Email invalid',
                'isLogged'   => false
            ], 422);
        }

        $token = $user->createToken($this->secretKey)->plainTextToken;

        
        return response([
            'user'       => $user,
            'token'      => $token,
            'isLogged'   => true,
        ], 201);

    }




    public function logout(Request $request) 
    {
        

        DB::table('personal_access_tokens')
        ->where('tokenable_id', $request->userId)
        ->delete();
          
        return response([
            'message' => 'Logged out'
        ],200);

    }

    
}
