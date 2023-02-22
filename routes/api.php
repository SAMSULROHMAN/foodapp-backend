<?php

use App\Http\Controllers\API\FoodController;
use App\Http\Controllers\API\MidtransController;
use App\Http\Controllers\API\TransactionControlller;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function(){
    Route::get('login',[UserController::class,'fetch']);
    Route::post('user',[UserController::class,'updateProfile']);
    Route::post('user/phote',[UserController::class,'updatePhoto']);
    Route::post('logout',[UserController::class,'logout']);
    Route::post('checkout',[TransactionControlller::class,'checkout']);
    Route::get('transaction',[TransactionControlller::class,'all']);
    Route::post('transaction/{id}',[TransactionControlller::class,'update']);
});


Route::post('login',[UserController::class,'login']);
Route::post('register',[UserController::class,'register']);
Route::get('food',[FoodController::class,'all']);
Route::post('midtrans/callback',[MidtransController::class,'callback']);