<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //
    public function login(Request $request)
        {        
        if(is_numeric($request->email)){
            $result = Auth::attempt(['no_ic' => $request->email, 'password' => $request->password]);
        }else {
            $result = Auth::attempt(['email' => $request->email, 'password' => $request->password]);
        }
        
        if (!$result) {
        return response()->json([
                'message' => 'Invalid login details',
                'access_token' => '',
                'token_type' => 'Bearer',
                ]);
            }

        if(is_numeric($request->email)){
            $user = \App\Models\User::where('no_ic', $request['email'])->firstOrFail();
        }else {
            $user = \App\Models\User::where('email', $request['email'])->firstOrFail();
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
                'message' => 'login success',
                'access_token' => $token,
                'token_type' => 'Bearer',
        ]);
        }

    public function createToken(Request $request)
        {
            
        $user = \App\Models\User::whereId($request->id)->firstOrFail();

        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([                
                'access_token' => $token,
                'token_type' => 'Bearer',
        ]);
        
        }
}
