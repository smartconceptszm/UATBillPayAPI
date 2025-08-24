<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


Route::get('/', function () use ($router) {
   return \env('APP_NAME').\env('APP_DESCRIPTION')." - is running";
});


Route::group(['middleware' => 'mode'], function (){

   //Auth Routes
      Route::post('/users', [\App\Http\Controllers\Bank\UserController::class,'store']);
      Route::post('/tokens', [\App\Http\Controllers\Auth\UserLoginController::class,'store']);
      Route::post('/passwordresets', [\App\Http\Controllers\Auth\UserPasswordResetController::class,'store']);
      Route::put('/passwordreset/{id}', [\App\Http\Controllers\Auth\UserPasswordResetController::class,'update']);
   //End

   // AUTHENTICATED Routes
      Route::group(['middleware' => 'auth'], function (){

         //Payments Via Bank API
            Route::get('customers/{id}', [\App\Http\Controllers\Bank\CustomerController::class, 'show']);
            Route::post('payments', [\App\Http\Controllers\Bank\PaymentsViaBankReceiptsController::class, 'store']);
         //

         // RBAC ROUTES 
            Route::controller(\App\Http\Controllers\Auth\UserController::class)->group(function () {
               Route::put('/users/{id}', 'update');
            });
         //
         
      });
   //
   
});