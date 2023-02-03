<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Create User
    public function createUser(Request $request){
        $validateUser = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required'
        ]);

        if($validateUser->false()){
            return response()->json([
                'status' => false,
                'message' => "Validation failed, Pleast try again.",
                'errors' => $validateUser->errors()
            ], 401);
        }
        

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => "User created successfully",
            'token' => $user->createToken("API TOKEN")->plainTextToken
        ],200);
    }

    //login user
    public function loginUser(Request $request){
        $validateUser = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validateUser->fails()){
            return response()->json([
                'status' => false,
                'message' => "Validation failed. please try again",
                'errors' => $validateUser->errors()
            ], 401);

            if(!Auth::attempt($request->only(['email', 'password']))){
                return response()->json([
                    'status' => false,
                    'message' => "Invalid credentials."
                ],401);
            }

            $user = User::where('email',$request->email)->first();
            return response()->json([
                'status' => true,
                'message' => "User logged in successfully",
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ],200);
        }
    }
}
