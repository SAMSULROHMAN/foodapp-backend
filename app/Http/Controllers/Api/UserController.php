<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    public function login(Request $request)
    {
        try {
            // Validasi Input
            $request->validate(
                [
                    'email' => 'email|required|',
                    'password' => 'required',
                ]
            );
            //Cek credentials
            $credentials = request(['email','password']);
            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error([
                    'messsage' => 'Unauthorized'
                ],'Authentication Failed',500);
            }

            // Jika hash tidak sesuai maka beri error
            
            $user = User::where('email',$request->email)->first();
            if(!Hash::check($request->password,$user->password)){
                throw new \Exception('Invalid Credentials');
            }

            // Success auth
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ],'Authenticated',200);

        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ],'Authenticated Failed',500);
        }
    }
}
