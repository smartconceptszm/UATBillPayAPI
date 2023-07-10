<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

Route::get('/users', [UserController::class, 'index']);

// Route::group(['prefix' => '/users'], function () {

//    Route::get('/', [
//                // 'middleware' => 'authorise:READ_USERS',
//                'uses' => 'UserController@index'
//            ]);

// });