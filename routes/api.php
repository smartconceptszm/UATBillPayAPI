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

   //Auth Routes
      Route::post('/login', [\App\Http\Controllers\Web\Auth\UserLoginController::class,'store']);
      Route::post('/passwordreset', [\App\Http\Controllers\Web\Auth\UserPasswordResetController::class,'store']);
      Route::put('/passwordreset/{id}', [\App\Http\Controllers\Web\Auth\UserPasswordResetController::class,'update']);
   //End

   //USSD Routes
      Route::get('/airtel', [\App\Http\Controllers\USSD\USSDAirtelController::class, 'index']);
      Route::get('/Zamtel', [\App\Http\Controllers\USSD\USSDZamtelController::class, 'index']);
      Route::get('/mtn',[\App\Http\Controllers\USSD\USSDMTNController::class, 'index']);
      //DEV Routes to Simulate /clientPrefix/mno
            Route::group(['prefix' => '/swasco'], function (){
               Route::get('/Zamtel', [\App\Http\Controllers\USSD\USSDZamtelController::class, 'index']);
               Route::get('/airtel', [\App\Http\Controllers\USSD\USSDAirtelController::class, 'index']);
               Route::get('/mtn', [\App\Http\Controllers\USSD\USSDMTNController::class, 'index']);
            });
            Route::group(['prefix' => '/lukanga'], function (){
               Route::get('/Zamtel', [\App\Http\Controllers\USSD\USSDZamtelController::class, 'index']);
               Route::get('/airtel', [\App\Http\Controllers\USSD\USSDAirtelController::class, 'index']);
               Route::get('/mtn', [\App\Http\Controllers\USSD\USSDMTNController::class, 'index']);
            });
            Route::group(['prefix' => '/chambeshi'], function (){
               Route::get('/Zamtel', [\App\Http\Controllers\USSD\USSDZamtelController::class, 'index']);
               Route::get('/airtel', [\App\Http\Controllers\USSD\USSDAirtelController::class, 'index']);
               Route::get('/mtn', [\App\Http\Controllers\USSD\USSDMTNController::class, 'index']);
            });

            Route::group(['prefix' => '/nkana'], function (){
               Route::get('/Zamtel', [\App\Http\Controllers\USSD\USSDZamtelController::class, 'index']);
               Route::get('/airtel', [\App\Http\Controllers\USSD\USSDAirtelController::class, 'index']);
               Route::get('/mtn', [\App\Http\Controllers\USSD\USSDMTNController::class, 'index']);
            });

            Route::group(['prefix' => '/kafubu'], function (){
               Route::get('/Zamtel', [\App\Http\Controllers\USSD\USSDZamtelController::class, 'index']);
               Route::get('/airtel', [\App\Http\Controllers\USSD\USSDAirtelController::class, 'index']);
               Route::get('/mtn', [\App\Http\Controllers\USSD\USSDMTNController::class, 'index']);
            });

            Route::group(['prefix' => '/mulonga'], function (){
               Route::get('/Zamtel', [\App\Http\Controllers\USSD\USSDZamtelController::class, 'index']);
               Route::get('/airtel', [\App\Http\Controllers\USSD\USSDAirtelController::class, 'index']);
               Route::get('/mtn', [\App\Http\Controllers\USSD\USSDMTNController::class, 'index']);
            });

            Route::group(['prefix' => '/scl'], function (){
               Route::get('/Zamtel', [\App\Http\Controllers\USSD\USSDZamtelController::class, 'index']);
               Route::get('/airtel', [\App\Http\Controllers\USSD\USSDAirtelController::class, 'index']);
               Route::get('/mtn', [\App\Http\Controllers\USSD\USSDMTNController::class, 'index']);
            });
      //End of Dev ROUTES
   //End

   //Mobile/Web/External Routes
      Route::get('/app/services', [\App\Http\Controllers\Web\Payments\PaymentsMenuController::class, 'index']);
      Route::get('/app/customers', [\App\Http\Controllers\Web\Payments\CustomerController::class, 'show']);
      Route::get('/app/paymentsproviders', [\App\Http\Controllers\Web\Payments\PaymentsProviderController::class, 'index']);
      Route::post('/app/paymentsviamomo', [\App\Http\Controllers\Web\Payments\PaymentViaMoMoController::class, 'store']);
      Route::post('/app/paymentsviacard', [\App\Http\Controllers\Web\Payments\PaymentViaCardController::class, 'store']);
   //End

   // AUTHENTICATED Routes
      Route::group(['middleware' => 'auth'], function (){
         //Payment Transactions Related Routes
            Route::get('maindashboard', [\App\Http\Controllers\Web\Clients\MainDashboardController::class, 'index']);
            Route::get('clientdashboard', [\App\Http\Controllers\Web\Clients\ClientDashboardController::class, 'index']);

            Route::get('paymenttransactions', [\App\Http\Controllers\Web\Payments\PaymentTransactionController::class, 'index']);
            Route::get('paymentsnotreceipted', [\App\Http\Controllers\Web\Payments\PaymentNotReceiptedController::class, 'index']);
            Route::post('receipts', [\App\Http\Controllers\Web\Payments\PaymentReceiptController::class, 'store']);
            Route::put('receipts/{id}', [\App\Http\Controllers\Web\Payments\PaymentReceiptController::class, 'update']);

            Route::put('paymentreceipts/{id}', [\App\Http\Controllers\Web\Payments\PaymentWithReceiptToDeliverController::class, 'update']);
            Route::post('batchpaymentreceipts', [\App\Http\Controllers\Web\Payments\BatchPaymentReceiptController::class, 'store']);

            Route::get('failedpayments', [\App\Http\Controllers\Web\Payments\PaymentFailedController::class, 'index']);
            Route::put('failedpayments/{id}', [\App\Http\Controllers\Web\Payments\PaymentFailedController::class, 'update']);
            Route::post('paymentsreviewbatch', [\App\Http\Controllers\Web\Payments\PaymentFailedBatchController::class, 'store']);
            
            Route::get('paymentsessions', [\App\Http\Controllers\Web\Payments\PaymentSessionController::class, 'index']);
            Route::get('sessionpayment', [\App\Http\Controllers\Web\Payments\PaymentController::class, 'findOneBy']);

            Route::controller(\App\Http\Controllers\Web\Payments\PaymentController::class)->group(function () {
               Route::get('/payments/findoneby', 'findOneBy');
               Route::get('/payments/{id}', 'show');
               Route::get('/payments', 'index');
               Route::put('/payments/{id}', 'update');
            });
         //
         //Clients
            Route::controller(\App\Http\Controllers\Web\Clients\ClientController::class)->group(function () {
               Route::get('/clients/findoneby', 'findOneBy');
               Route::put('/clients/{id}', 'update');
               Route::get('/clients/{id}', 'show');
               Route::post('/clients', 'store');
               Route::get('/clients', 'index');
            });
         //

         //Client Billing Credentials
            Route::controller(\App\Http\Controllers\Web\Clients\BillingCredentialController::class)->group(function () {
               Route::get('/billingcredentials/findoneby', 'findOneBy');
               Route::put('/billingcredentials/{id}', 'update');
               Route::get('/billingcredentials/{id}', 'show');
               Route::post('/billingcredentials', 'store');
               Route::get('/billingcredentials', 'index');
            });
            Route::controller(\App\Http\Controllers\Web\Clients\BillingCredentialController::class)->group(function () {
               Route::get('/billingcredentialsofclient/{id}', 'credentialsofclient');
            });
         //
         
         //Menus
            Route::get('rootmenu', [\App\Http\Controllers\Web\Clients\ClientMenuController::class,'findOneBy']);
            Route::controller(\App\Http\Controllers\Web\Clients\ClientMenuController::class)->group(function () {
               Route::get('/clientmenus/findoneby', 'findOneBy');
               Route::put('/clientmenus/{id}', 'update');
               Route::get('/clientmenus/{id}', 'show');
               Route::get('/menusofclient/{id}', 'menusofclient');
               Route::post('/clientmenus', 'store');
               Route::get('/clientmenus', 'index');
            });
         //

         //MNOs
            Route::controller(\App\Http\Controllers\Web\Clients\MNOController::class)->group(function () {
               Route::get('/mnos/findoneby', 'findOneBy');
               Route::put('/mnos/{id}', 'update');
               Route::get('/mnos/{id}', 'show');
               Route::post('/mnos', 'store');
               Route::get('/mnos', 'index');
            });
         //

         //Client MNOs
            Route::controller(\App\Http\Controllers\Web\Clients\ClientMnoController::class)->group(function () {
               Route::get('/clientmnos/findoneby', 'findOneBy');
               Route::put('/clientmnos/{id}', 'update');
               Route::get('/clientmnos/{id}', 'show');
               Route::post('/clientmnos', 'store');
               Route::get('/clientmnos', 'index');
            });

            Route::controller(\App\Http\Controllers\Web\Clients\MnosOfClientController::class)->group(function () {
               Route::get('/mnosofclient/{id}', 'index');
            });

         //

         Route::controller(\App\Http\Controllers\Web\Clients\MnosOfClientController::class)->group(function () {
            Route::get('/mnosofclient/{id}', 'index');
         });


         //PAYMENTS PROVIDERS
            Route::controller(\App\Http\Controllers\Web\Clients\PaymentsProviderController::class)->group(function () {
               Route::get('/paymentsproviders/findoneby', 'findOneBy');
               Route::put('/paymentsproviders/{id}', 'update');
               Route::get('/paymentsproviders/{id}', 'show');
               Route::post('/paymentsproviders', 'store');
               Route::get('/paymentsproviders', 'index');
            });
         //

         //Client PAYMENTS PROVIDERS
            Route::controller(\App\Http\Controllers\Web\Clients\ClientWalletController::class)->group(function () {
               Route::get('/clientwallets/findoneby', 'findOneBy');
               Route::put('/clientwallets/{id}', 'update');
               Route::get('/clientwallets/{id}', 'show');
               Route::post('/clientwallets', 'store');
               Route::get('/clientwallets', 'index');
            });

         //

         Route::controller(\App\Http\Controllers\Web\Clients\ClientWalletController::class)->group(function () {
            Route::get('/walletsofclient/{id}', 'walletsofclient');
         });

         //Client Wallet Credentials
            Route::controller(\App\Http\Controllers\Web\Clients\ClientWalletCredentialController::class)->group(function () {
               Route::get('/walletcredentials/findoneby', 'findOneBy');
               Route::put('/walletcredentials/{id}', 'update');
               Route::get('/walletcredentials/{id}', 'show');
               Route::post('/walletcredentials', 'store');
               Route::get('/walletcredentials', 'index');
            });
            Route::controller(\App\Http\Controllers\Web\Clients\ClientWalletCredentialController::class)->group(function () {
               Route::get('/credentialsofwallet/{id}', 'credentialsofwallet');
            });
         //
   
         //Complaint
            Route::get('complaintsdashboard', [\App\Http\Controllers\Web\CRM\ComplaintDashboardController::class, 'index']);
            Route::get('complaintsofclient', [\App\Http\Controllers\Web\CRM\ComplaintsOfClientController::class, 'index']);
            Route::controller(\App\Http\Controllers\Web\CRM\ComplaintController::class)->group(function () {
               Route::get('/complaints/findoneby', 'findOneBy');
               Route::put('/complaints/{id}', 'update');
               Route::get('/complaints/{id}', 'show');
               Route::post('/complaintypes', 'store');
               Route::get('/complaintypes', 'index');
            });
         //
         //Complaint Types
            Route::get('complainttypesofclient', [\App\Http\Controllers\Web\MenuConfigs\ComplaintTypeController::class, 'index']);
            Route::controller(\App\Http\Controllers\Web\MenuConfigs\ComplaintTypeController::class)->group(function () {
               Route::get('/complainttypes/findoneby', 'findOneBy');
               Route::put('/complainttypes/{id}', 'update');
               Route::get('/complainttypes/{id}', 'show');
               Route::post('/complainttypes', 'store');
               Route::get('/complainttypes', 'index');
            });
         //
         //Complaint Sub Types
            Route::controller(\App\Http\Controllers\Web\MenuConfigs\ComplaintSubTypeController::class)->group(function () {
               Route::get('/complaintsubtypes/findoneby', 'findOneBy');
               Route::put('/complaintsubtypes/{id}', 'update');
               Route::delete('/complaintsubtypes/{id}', 'destroy');
               Route::get('/complaintsubtypes/{id}', 'show');
               Route::post('/complaintsubtypes', 'store');
               Route::get('/complaintsubtypes', 'index');
            });
         //
         // Text Messaging
            Route::get('smsdashboard', [\App\Http\Controllers\Web\SMS\SMSDashboardController::class, 'index']);
            Route::get('smses', [\App\Http\Controllers\Web\SMS\SMSesOfClientController::class, 'index']);
            Route::post('messages', [\App\Http\Controllers\Web\SMS\MessageController::class, 'store']);
            Route::post('messages/bulk', [\App\Http\Controllers\Web\SMS\SMSBulkController::class, 'store']);
            Route::post('messages/bulkcustom', [\App\Http\Controllers\Web\SMS\SMSBulkCustomController::class, 'store']);
         //
         // Surveys
            Route::get('activesurveyquestions', [\App\Http\Controllers\Web\CRM\ActiveSurveyQuestionsController::class, 'index']);
            Route::get('surveyresponsesofquestion', [\App\Http\Controllers\Web\CRM\SurveyResponsesOfQuestionController::class, 'index']);
            Route::controller(\App\Http\Controllers\Web\MenuConfigs\SurveyController::class)->group(function () {
               Route::get('/surveys/findoneby', 'findOneBy');
               Route::put('/surveys/{id}', 'update');
               Route::get('/surveys/{id}', 'show');
               Route::post('/surveys', 'store');
               Route::get('/surveys', 'index');
            });

            Route::controller(\App\Http\Controllers\Web\MenuConfigs\SurveyQuestionController::class)->group(function () {
               Route::get('/surveyquestions/findoneby', 'findOneBy');
               Route::put('/surveyquestions/{id}', 'update');
               Route::get('/surveyquestions/{id}', 'show');
               Route::post('/surveyquestions', 'store');
               Route::get('/surveyquestions', 'index');
            });

            Route::controller(\App\Http\Controllers\Web\MenuConfigs\SurveyQuestionListTypeController::class)->group(function () {
               Route::get('/surveyquestionlisttypes/findoneby', 'findOneBy');
               Route::put('/surveyquestionlisttypes/{id}', 'update');
               Route::get('/surveyquestionlisttypes/{id}', 'show');
               Route::post('/surveyquestionlisttypes', 'store');
               Route::get('/surveyquestionlisttypes', 'index');
            });

            Route::controller(\App\Http\Controllers\Web\MenuConfigs\SurveyQuestionListItemController::class)->group(function () {
               Route::get('/surveyquestionlistitems/findoneby', 'findOneBy');
               Route::delete('/surveyquestionlistitems/{id}', 'destroy');
               Route::put('/surveyquestionlistitems/{id}', 'update');
               Route::get('/surveyquestionlistitems/{id}', 'show');
               Route::post('/surveyquestionlistitems', 'store');
               Route::get('/surveyquestionlistitems', 'index');
            });

         //
         // Sessions 
            Route::controller(\App\Http\Controllers\Web\Sessions\SessionController::class)->group(function () {
               Route::get('/sessions', 'index');
               Route::get('/sessions/{id}', 'show');
               Route::put('/sessions/{id}', 'update');
            });
            Route::get('sessionsofclient', [\App\Http\Controllers\Web\Sessions\SessionofClientController::class, 'index']);

         //
         // RBAC ROUTES 
            Route::get('usersofclient/{id}', [\App\Http\Controllers\Web\Auth\UsersOfClientController::class, 'index']);      
            Route::get('groupsofuser/{id}', [\App\Http\Controllers\Web\Auth\GroupsOfUserController::class, 'index']);
            Route::get('groupsofclient/{id}', [\App\Http\Controllers\Web\Auth\GroupsOfClientController::class, 'index']);
            Route::get('rightsofgroup/{id}', [\App\Http\Controllers\Web\Auth\RightsOfGroupController::class, 'index']);
            Route::get('rightsofuser', [\App\Http\Controllers\Web\Auth\RightsOfUserController::class, 'index']);

            Route::controller(\App\Http\Controllers\Web\Auth\UserController::class)->group(function () {
               Route::get('/users/findoneby', 'findOneBy');
               Route::put('/users/{id}', 'update');
               Route::get('/users/{id}', 'show');
               Route::post('/users', 'store');
               Route::get('/users', 'index');
            });

            Route::controller(\App\Http\Controllers\Web\Auth\UserGroupController::class)->group(function () {
               Route::get('/usergroups/findoneby', 'findOneBy');
               Route::delete('/usergroups/{id}', 'destroy');
               Route::put('/usergroups/{id}', 'update');
               Route::get('/usergroups/{id}', 'show');
               Route::post('/usergroups', 'store');
               Route::get('/usergroups', 'index');
            });

            Route::controller(\App\Http\Controllers\Web\Auth\GroupController::class)->group(function () {
               Route::get('/groups/findoneby', 'findOneBy');
               Route::delete('/groups/{id}', 'destroy');
               Route::put('/groups/{id}', 'update');
               Route::get('/groups/{id}', 'show');
               Route::post('/groups', 'store');
               Route::get('/groups', 'index');
            });

            Route::controller(\App\Http\Controllers\Web\Auth\RightController::class)->group(function () {
               Route::get('/rights/findoneby', 'findOneBy');
               //Route::delete('/rights/{id}', 'destroy');
               Route::put('/rights/{id}', 'update');
               Route::get('/rights/{id}', 'show');
               Route::post('/rights', 'store');
               Route::get('/rights', 'index');
            });

            Route::controller(\App\Http\Controllers\Web\Auth\GroupRightController::class)->group(function () {
               Route::get('/grouprights/findoneby', 'findOneBy');
               Route::delete('/grouprights/{id}', 'destroy');
               Route::put('/grouprights/{id}', 'update');
               Route::get('/grouprights/{id}', 'show');
               Route::post('/grouprights', 'store');
               Route::get('/grouprights', 'index');
            });

            Route::delete('/logout', [\App\Http\Controllers\Web\Auth\UserLogoutController::class,'destroy']);
         //

      });
   //
   
});