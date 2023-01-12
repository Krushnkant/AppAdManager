<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PassportAuthController;
use App\Http\Controllers\admin\ContactusController;
use App\Http\Controllers\API\UserProfileController;
use App\Http\Controllers\API\SuggestionController;
use App\Http\Controllers\API\SocialProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [\App\Http\Controllers\API\AuthController::class, 'login']);
Route::post('send_otp',[\App\Http\Controllers\API\UserController::class,'send_otp']);
Route::post('verify_otp',[\App\Http\Controllers\API\UserController::class,'verify_otp']);

Route::post('register-user',[\App\Http\Controllers\API\UserController::class,'registerUser']);
Route::post('splash',[\App\Http\Controllers\API\UserController::class,'splash']);
Route::post('app-data',[\App\Http\Controllers\API\AppController::class,'appData']);
Route::post('ad-request',[\App\Http\Controllers\API\AppController::class,'adRequest']);
Route::post('update-ad-status',[\App\Http\Controllers\API\AppController::class,'updateAdStatus']);

Route::post('package-purchase',[\App\Http\Controllers\API\AppController::class,'packagePurchase']);
Route::post('contact-message',[\App\Http\Controllers\API\UserController::class,'contactMessage']);

Route::post('get-packages',[\App\Http\Controllers\API\AppController::class,'getPackages']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

