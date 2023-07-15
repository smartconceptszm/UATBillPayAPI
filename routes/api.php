<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
// use ;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/', function () use ($router) {

   if(\env("APP_MODE") == 'UP'){
      return \env('APP_NAME').\env('APP_DESCRIPTION')." - is running";
   }else{
      return \env("MODE_MESSAGE");
   }

});

Route::group(['middleware' => 'mode'], function (){
   //USSD ROUTES
      Route::get('/airtel', [\App\Http\Controllers\USSD\USSDAirtelController::class, 'index']);
      Route::get('/zamtel', [\App\Http\Controllers\USSD\USSDZamtelController::class, 'index']);
      Route::get('/mtn',[\App\Http\Controllers\USSD\USSDMTNController::class, 'index']);
      //DEV ROUTES to Simulate /clientPrefix/mno
            Route::group(['prefix' => '/chambeshi'], function (){
               Route::get('/zamtel', [\App\Http\Controllers\USSD\USSDZamtelController::class, 'index']);
               Route::get('/airtel', [\App\Http\Controllers\USSD\USSDAirtelController::class, 'index']);
               Route::get('/mtn', [\App\Http\Controllers\USSD\USSDMTNController::class, 'index']);
            });
            Route::group(['prefix' => '/lukanga'], function (){
               Route::get('/zamtel', [\App\Http\Controllers\USSD\USSDZamtelController::class, 'index']);
               Route::get('/airtel', [\App\Http\Controllers\USSD\USSDAirtelController::class, 'index']);
               Route::get('/mtn', [\App\Http\Controllers\USSD\USSDMTNController::class, 'index']);
            });
            Route::group(['prefix' => '/mulonga'], function (){
               Route::get('/zamtel', [\App\Http\Controllers\USSD\USSDZamtelController::class, 'index']);
               Route::get('/airtel', [\App\Http\Controllers\USSD\USSDAirtelController::class, 'index']);
               Route::get('/mtn', [\App\Http\Controllers\USSD\USSDMTNController::class, 'index']);
            });
            Route::group(['prefix' => '/swasco'], function (){
               Route::get('/zamtel', [\App\Http\Controllers\USSD\USSDZamtelController::class, 'index']);
               Route::get('/airtel', [\App\Http\Controllers\USSD\USSDAirtelController::class, 'index']);
               Route::get('/mtn', [\App\Http\Controllers\USSD\USSDMTNController::class, 'index']);
            });
      //End of Dev ROUTES
   //End of USSD Routes
   //  Route::get('/users', [\App\Http\Controllers\Auth\UserController::class, 'index']);
   Route::controller(\App\Http\Controllers\Auth\UserController::class)->group(function () {
      Route::get('/users/findoneby', 'findOneBy');
      Route::get('/users/{id}', 'show');
      Route::get('/users', 'index');
      Route::post('/users', 'store');
   });

   Route::controller(\App\Http\Controllers\SessionController::class)->group(function () {
      Route::get('/sessions', 'index');
      Route::get('/sessions/{id}', 'show');
   });

});