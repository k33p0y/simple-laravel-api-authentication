<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Termwind\Components\Raw;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{
    // Register API (POST, formdata)
    public function register(Request $request){
        $request->validate([
            "name" => "required",
            "email" => "required|email|unique:users",
            "password" => "required|confirmed"
        ]);

        User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
        ]);

        return response()->json([
            "status" => true,
            "message" => "User registered successfuly"
        ]);
    }

    // Login API (POST, formdata)
    public function login(Request $request){
        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        $user = User::where("email", $request->email)->first();
        if(!empty($user)){
            // User exists
            if(Hash::check($request->password, $user->password)){

                $token = $user->createToken("myToken")->plainTextToken;
                return response()->json([
                    "status" => true,
                    "message" => "Login successful.",
                    "token" => $token
                ]);
            }

            return response()->json([
                "status" => false,
                "message" => "Password didn't match"
            ]);
        }

        return response()->json([
            "status" => false,
            "message" => "Invalid login details"
        ]);
    }

    // Profile API (GET)
    public function profile(){
        $data = auth()->user();
        return response()->json([
            "status" => true,
            "message" => "Profile data",
            "user" => $data
        ]);
    }

    // Logout API (GET)
    public function logout(){
        auth()->user()->tokens()->delete();

        return response()->json([
            "status" => true,
            "message" => "User logged out"
        ]);
    }
}
