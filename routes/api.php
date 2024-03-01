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
            Route::group(['prefix' => '/swasco'], function (){
               Route::get('/zamtel', [\App\Http\Controllers\USSD\USSDZamtelController::class, 'index']);
               Route::get('/airtel', [\App\Http\Controllers\USSD\USSDAirtelController::class, 'index']);
               Route::get('/mtn', [\App\Http\Controllers\USSD\USSDMTNController::class, 'index']);
            });
            Route::group(['prefix' => '/lukanga'], function (){
               Route::get('/zamtel', [\App\Http\Controllers\USSD\USSDZamtelController::class, 'index']);
               Route::get('/airtel', [\App\Http\Controllers\USSD\USSDAirtelController::class, 'index']);
               Route::get('/mtn', [\App\Http\Controllers\USSD\USSDMTNController::class, 'index']);
            });
            Route::group(['prefix' => '/chambeshi'], function (){
               Route::get('/zamtel', [\App\Http\Controllers\USSD\USSDZamtelController::class, 'index']);
               Route::get('/airtel', [\App\Http\Controllers\USSD\USSDAirtelController::class, 'index']);
               Route::get('/mtn', [\App\Http\Controllers\USSD\USSDMTNController::class, 'index']);
            });

            Route::group(['prefix' => '/mulonga'], function (){
               Route::get('/zamtel', [\App\Http\Controllers\USSD\USSDZamtelController::class, 'index']);
               Route::get('/airtel', [\App\Http\Controllers\USSD\USSDAirtelController::class, 'index']);
               Route::get('/mtn', [\App\Http\Controllers\USSD\USSDMTNController::class, 'index']);
            });

            Route::group(['prefix' => '/mazabuka'], function (){
               Route::get('/zamtel', [\App\Http\Controllers\USSD\USSDZamtelController::class, 'index']);
               Route::get('/airtel', [\App\Http\Controllers\USSD\USSDAirtelController::class, 'index']);
               Route::get('/mtn', [\App\Http\Controllers\USSD\USSDMTNController::class, 'index']);
            });
      //End of Dev ROUTES
   //End of USSD Routes
   //Auth
      Route::post('/login', [\App\Http\Controllers\Auth\UserLoginController::class,'store']);
      Route::post('/passwordreset', [\App\Http\Controllers\Auth\UserPasswordResetController::class,'store']);
      Route::put('/passwordreset/{id}', [\App\Http\Controllers\Auth\UserPasswordResetController::class,'update']);

   //Web Payment Routes - SWASCO
      Route::get('/webpayments/swasco/paymentmenus', [\App\Http\Controllers\Web\PaymentMenuController::class, 'index']);
      Route::get('/webpayments/swasco/customers/{accountNumber}', [\App\Http\Controllers\Web\WebPaymentController::class, 'show']);
      Route::get('/webpayments/swasco/momos', [\App\Http\Controllers\Web\PaymentMnoController::class, 'index']);
      Route::post('/webpayments/swasco/payments', [\App\Http\Controllers\Web\WebPaymentController::class, 'store']);
   //

   Route::group(['middleware' => 'auth'], function (){
      //Payment Transactions Related Routes
         Route::get('maindashboard', [\App\Http\Controllers\Clients\MainDashboardController::class, 'index']);
         Route::get('clientdashboard', [\App\Http\Controllers\Clients\ClientDashboardController::class, 'index']);
         Route::get('paymenttransactions', [\App\Http\Controllers\Payments\PaymentTransactionController::class, 'index']);

         Route::get('paymentsnotreceipted', [\App\Http\Controllers\Payments\PaymentNotReceiptedController::class, 'index']);
         Route::post('receipts', [\App\Http\Controllers\Payments\PaymentReceiptController::class, 'store']);
         Route::put('receipts/{id}', [\App\Http\Controllers\Payments\PaymentReceiptController::class, 'update']);
         Route::put('paymentreceipts/{id}', [\App\Http\Controllers\Payments\PaymentWithReceiptToDeliverController::class, 'update']);
         Route::post('batchpaymentreceipts', [\App\Http\Controllers\Payments\BatchPaymentReceiptController::class, 'store']);

         Route::get('failedpayments', [\App\Http\Controllers\Payments\PaymentFailedController::class, 'index']);
         Route::put('failedpayments/{id}', [\App\Http\Controllers\Payments\PaymentFailedController::class, 'update']);
         Route::post('paymentsreviewbatch', [\App\Http\Controllers\Payments\PaymentFailedBatchController::class, 'store']);
         
         Route::get('paymentsessions', [\App\Http\Controllers\Payments\PaymentSessionController::class, 'index']);

         Route::post('paymentstomanuallypost', [\App\Http\Controllers\Payments\PaymentManualPostController::class, 'store']);

         Route::controller(\App\Http\Controllers\Payments\PaymentController::class)->group(function () {
            Route::get('/payments/findoneby', 'findOneBy');
            Route::get('/payments/{id}', 'show');
            Route::get('/payments', 'index');
            Route::post('/payments', 'store');
            Route::put('/payments/{id}', 'update');
         });
      //
      //Clients
         Route::controller(\App\Http\Controllers\Clients\ClientController::class)->group(function () {
            Route::get('/clients/findoneby', 'findOneBy');
            Route::put('/clients/{id}', 'update');
            Route::get('/clients/{id}', 'show');
            Route::post('/clients', 'store');
            Route::get('/clients', 'index');
         });
      //
      //Menus
         Route::controller(\App\Http\Controllers\Clients\ClientMenuController::class)->group(function () {
            Route::get('/clientmenus/findoneby', 'findOneBy');
            Route::put('/clientmenus/{id}', 'update');
            Route::get('/clientmenus/{id}', 'show');
            Route::get('/menusofclient/{id}', 'menusofclient');
            Route::post('/clientmenus', 'store');
            Route::get('/clientmenus', 'index');
         });
      //
      //MNOs
         Route::controller(\App\Http\Controllers\Clients\MNOController::class)->group(function () {
            Route::get('/mnos/findoneby', 'findOneBy');
            Route::put('/mnos/{id}', 'update');
            Route::get('/mnos/{id}', 'show');
            Route::post('/mnos', 'store');
            Route::get('/mnos', 'index');
         });
      //
      //Client MNOs
         Route::controller(\App\Http\Controllers\Clients\ClientMnoController::class)->group(function () {
            Route::get('/  /findoneby', 'findOneBy');
            Route::put('/clientmnos/{id}', 'update');
            Route::get('/clientmnos/{id}', 'show');
            Route::post('/clientmnos', 'store');
            Route::get('/clientmnos', 'index');
         });

         Route::controller(\App\Http\Controllers\Clients\MnosOfClientController::class)->group(function () {
            Route::get('/mnosofclient/{id}', 'index');
         });

      //
      //Complaint
         Route::get('complaintsdashboard', [\App\Http\Controllers\CRM\ComplaintDashboardController::class, 'index']);
         Route::get('complaintsofclient', [\App\Http\Controllers\CRM\ComplaintsOfClientController::class, 'index']);
         Route::controller(\App\Http\Controllers\CRM\ComplaintController::class)->group(function () {
            Route::get('/complaints/findoneby', 'findOneBy');
            Route::put('/complaints/{id}', 'update');
            Route::get('/complaints/{id}', 'show');
            Route::post('/complaintypes', 'store');
            Route::get('/complaintypes', 'index');
         });
      //
      //Complaint Types
         Route::controller(\App\Http\Controllers\MenuConfigs\ComplaintTypeController::class)->group(function () {
            Route::get('/complainttypes/findoneby', 'findOneBy');
            Route::put('/complainttypes/{id}', 'update');
            Route::get('/complainttypes/{id}', 'show');
            Route::post('/complainttypes', 'store');
            Route::get('/complainttypes', 'index');
         });
      //
      //Complaint Sub Types
         Route::controller(\App\Http\Controllers\MenuConfigs\ComplaintSubTypeController::class)->group(function () {
            Route::get('/complaintsubtypes/findoneby', 'findOneBy');
            Route::put('/complaintsubtypes/{id}', 'update');
            Route::delete('/complaintsubtypes/{id}', 'destroy');
            Route::get('/complaintsubtypes/{id}', 'show');
            Route::post('/complaintsubtypes', 'store');
            Route::get('/complaintsubtypes', 'index');
         });
      //
      // Text Messaging
         Route::get('smsdashboard', [\App\Http\Controllers\SMS\SMSDashboardController::class, 'index']);
         Route::get('smses', [\App\Http\Controllers\SMS\SMSesOfClientController::class, 'index']);
         Route::post('messages', [\App\Http\Controllers\SMS\MessageController::class, 'store']);
         Route::post('messages/bulk', [\App\Http\Controllers\SMS\SMSBulkController::class, 'store']);
         Route::post('messages/bulkcustom', [\App\Http\Controllers\SMS\SMSBulkCustomController::class, 'store']);
      //
      //Surveys
         Route::get('activesurveyquestions', [\App\Http\Controllers\CRM\ActiveSurveyQuestionsController::class, 'index']);
         Route::get('surveyresponsesofquestion', [\App\Http\Controllers\CRM\SurveyResponsesOfQuestionController::class, 'index']);
         Route::controller(\App\Http\Controllers\MenuConfigs\SurveyController::class)->group(function () {
            Route::get('/surveys/findoneby', 'findOneBy');
            Route::put('/surveys/{id}', 'update');
            Route::get('/surveys/{id}', 'show');
            Route::post('/surveys', 'store');
            Route::get('/surveys', 'index');
         });

         Route::controller(\App\Http\Controllers\MenuConfigs\SurveyQuestionController::class)->group(function () {
            Route::get('/surveyquestions/findoneby', 'findOneBy');
            Route::put('/surveyquestions/{id}', 'update');
            Route::get('/surveyquestions/{id}', 'show');
            Route::post('/surveyquestions', 'store');
            Route::get('/surveyquestions', 'index');
         });

         Route::controller(\App\Http\Controllers\MenuConfigs\SurveyQuestionListTypeController::class)->group(function () {
            Route::get('/surveyquestionlisttypes/findoneby', 'findOneBy');
            Route::put('/surveyquestionlisttypes/{id}', 'update');
            Route::get('/surveyquestionlisttypes/{id}', 'show');
            Route::post('/surveyquestionlisttypes', 'store');
            Route::get('/surveyquestionlisttypes', 'index');
         });

         Route::controller(\App\Http\Controllers\MenuConfigs\SurveyQuestionListItemController::class)->group(function () {
            Route::get('/surveyquestionlistitems/findoneby', 'findOneBy');
            Route::delete('/surveyquestionlistitems/{id}', 'destroy');
            Route::put('/surveyquestionlistitems/{id}', 'update');
            Route::get('/surveyquestionlistitems/{id}', 'show');
            Route::post('/surveyquestionlistitems', 'store');
            Route::get('/surveyquestionlistitems', 'index');
         });

      //
      //Sessions 
         Route::controller(\App\Http\Controllers\USSD\SessionController::class)->group(function () {
            Route::get('/sessions', 'index');
            Route::get('/sessions/{id}', 'show');
         });
      //
      // RBAC ROUTES 
         Route::get('usersofclient', [\App\Http\Controllers\Auth\UsersOfClientController::class, 'index']);      
         Route::get('groupsofuser/{id}', [\App\Http\Controllers\Auth\GroupsOfUserController::class, 'index']);
         Route::get('groupsofclient/{id}', [\App\Http\Controllers\Auth\GroupsOfClientController::class, 'index']);
         Route::get('rightsofgroup/{id}', [\App\Http\Controllers\Auth\RightsOfGroupController::class, 'index']);
         Route::get('rightsofuser', [\App\Http\Controllers\Auth\RightsOfUserController::class, 'index']);

         Route::controller(\App\Http\Controllers\Auth\UserController::class)->group(function () {
            Route::get('/users/findoneby', 'findOneBy');
            Route::put('/users/{id}', 'update');
            Route::get('/users/{id}', 'show');
            Route::post('/users', 'store');
            Route::get('/users', 'index');
         });

         Route::controller(\App\Http\Controllers\Auth\UserGroupController::class)->group(function () {
            Route::get('/usergroups/findoneby', 'findOneBy');
            Route::delete('/usergroups/{id}', 'destroy');
            Route::put('/usergroups/{id}', 'update');
            Route::get('/usergroups/{id}', 'show');
            Route::post('/usergroups', 'store');
            Route::get('/usergroups', 'index');
         });

         Route::controller(\App\Http\Controllers\Auth\GroupController::class)->group(function () {
            Route::get('/groups/findoneby', 'findOneBy');
            Route::delete('/groups/{id}', 'destroy');
            Route::put('/groups/{id}', 'update');
            Route::get('/groups/{id}', 'show');
            Route::post('/groups', 'store');
            Route::get('/groups', 'index');
         });

         Route::controller(\App\Http\Controllers\Auth\RightController::class)->group(function () {
            Route::get('/rights/findoneby', 'findOneBy');
            //Route::delete('/rights/{id}', 'destroy');
            Route::put('/rights/{id}', 'update');
            Route::get('/rights/{id}', 'show');
            Route::post('/rights', 'store');
            Route::get('/rights', 'index');
         });

         Route::controller(\App\Http\Controllers\Auth\GroupRightController::class)->group(function () {
            Route::get('/grouprights/findoneby', 'findOneBy');
            Route::delete('/grouprights/{id}', 'destroy');
            Route::put('/grouprights/{id}', 'update');
            Route::get('/grouprights/{id}', 'show');
            Route::post('/grouprights', 'store');
            Route::get('/grouprights', 'index');
         });

         Route::delete('/logout', [\App\Http\Controllers\Auth\UserLogoutController::class,'destroy']);
      //

   });
});