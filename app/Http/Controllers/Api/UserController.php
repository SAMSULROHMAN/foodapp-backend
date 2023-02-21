<?php

namespace App\Http\Controllers\Api;

use App\Actions\Fortify\PasswordValidationRules;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    
    use PasswordValidationRules;

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

    public function register(Request $request)
    {
        try {
            // validasi
            $request->validate([
                'name' => ['required','string','max:255'],
                'email' => ['required','string','email','max:255','unique:users'],
                'password' => $this->passwordRules()
            ]);

            //Create User
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'address' => $request->address,
                'houseNumber' => $request->houseNumber,
                'phoneNUmber' => $request->phoneNumber,
                'city' => $request->city,
                'password' => Hash::make($request->password),
            ]);

            $user = User::where('email',$request->email)->first();

            // Get Token
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ]);

        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ],'Authenticated Failed',500);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken()->delete();
        
        return ResponseFormatter::success($token,'Token Revoked');
    }
}
