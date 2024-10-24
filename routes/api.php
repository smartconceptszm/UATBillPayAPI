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
   $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
   if($billpaySettings['APP_MODE']==='UP'){
      return \env('APP_NAME').\env('APP_DESCRIPTION')." - is running";
   }else{
       return $billpaySettings['MODE_MESSAGE'];
   }
});

Route::group(['middleware' => 'mode'], function (){

   //Auth Routes
      Route::post('/login', [\App\Http\Controllers\Auth\UserLoginController::class,'store']);
      Route::post('/passwordreset', [\App\Http\Controllers\Auth\UserPasswordResetController::class,'store']);
      Route::put('/passwordreset/{id}', [\App\Http\Controllers\Auth\UserPasswordResetController::class,'update']);
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

            Route::group(['prefix' => '/mazabuka'], function (){
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

   //MoMo Callback Routes
      Route::post('/zamtelkwacha/callback', [\App\Http\Controllers\Gateway\MoMoCallbackController::class, 'zamtel']);
      Route::post('/airtelmoney/callback', [\App\Http\Controllers\Gateway\MoMoCallbackController::class, 'airtel']);
      Route::post('/mtnmomo/callback',[\App\Http\Controllers\Gateway\MoMoCallbackController::class, 'mtn']);
   //

   //Mobile/Web/External Gateway Routes
      Route::get('/app/services', [\App\Http\Controllers\Gateway\PaymentsMenuController::class, 'index']);
      Route::get('/app/customers', [\App\Http\Controllers\Gateway\CustomerController::class, 'show']);
      Route::get('/app/paymentsprovidersofclient', [\App\Http\Controllers\Gateway\PaymentsProvidersOfClientController::class, 'index']);
      Route::get('/app/clientwallets/findoneby', [\App\Http\Controllers\Clients\ClientWalletController::class, 'findOneBy']);
      Route::post('/app/paymentsviamomo', [\App\Http\Controllers\Gateway\PaymentViaMoMoController::class, 'store']);
      Route::post('/app/paymentsviacard', [\App\Http\Controllers\Gateway\PaymentViaCardController::class, 'store']);
      Route::get('/app/payments/{id}', [\App\Http\Controllers\Payments\PaymentSessionController::class, 'show']);
   //End

   // AUTHENTICATED Routes
      Route::group(['middleware' => 'auth'], function (){
         //Analaytics
            Route::get('maindashboard', [\App\Http\Controllers\Analytics\MainDashboardController::class, 'index']);
            Route::get('clientdashboard', [\App\Http\Controllers\Analytics\ClientDashboardController::class, 'index']);
            Route::get('clientmonthlydashboard', [\App\Http\Controllers\Analytics\ClientMonthlyDashboardController::class, 'index']);

            Route::controller(\App\Http\Controllers\Analytics\DailyAnalyticsController::class)->group(function () {
               Route::get('/analytics/daily', 'index');
               Route::post('/analytics/daily', 'generate');
            });
            Route::controller(\App\Http\Controllers\Analytics\MonthlyAnalyticsController::class)->group(function () {
               Route::get('/analytics/monthly', 'index');
               Route::post('/analytics/monthly', 'generate');
            });
         //

         //Payment Transactions Related Routes
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
            Route::get('sessionpayment', [\App\Http\Controllers\Payments\PaymentController::class, 'findOneBy']);

            Route::controller(\App\Http\Controllers\Payments\PaymentController::class)->group(function () {
               Route::get('/payments/findoneby', 'findOneBy');
               Route::get('/payments/{id}', 'show');
               Route::get('/payments', 'index');
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

         //Client Billing Credentials
            Route::controller(\App\Http\Controllers\Clients\BillingCredentialController::class)->group(function () {
               Route::get('/billingcredentials/findoneby', 'findOneBy');
               Route::put('/billingcredentials/{id}', 'update');
               Route::get('/billingcredentials/{id}', 'show');
               Route::post('/billingcredentials', 'store');
               Route::get('/billingcredentials', 'index');
            });
            Route::controller(\App\Http\Controllers\Clients\BillingCredentialController::class)->group(function () {
               Route::get('/billingcredentialsofclient/{id}', 'credentialsofclient');
            });
         //
         
         //Menus
            Route::get('rootmenu', [\App\Http\Controllers\Clients\ClientMenuController::class,'findOneBy']);
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
               Route::get('/clientmnos/findoneby', 'findOneBy');
               Route::put('/clientmnos/{id}', 'update');
               Route::get('/clientmnos/{id}', 'show');
               Route::post('/clientmnos', 'store');
               Route::get('/clientmnos', 'index');
            });

            Route::controller(\App\Http\Controllers\Clients\MnosOfClientController::class)->group(function () {
               Route::get('/mnosofclient/{id}', 'index');
            });
         //

         //SMS Channel Credentials
            Route::controller(\App\Http\Controllers\Clients\ClientMnoCredentialController::class)->group(function () {
               Route::get('/smschannelcredentials/findoneby', 'findOneBy');
               Route::put('/smschannelcredentials/{id}', 'update');
               Route::get('/smschannelcredentials/{id}', 'show');
               Route::post('/smschannelcredentials', 'store');
               Route::get('/smschannelcredentials', 'index');
            });
            Route::controller(\App\Http\Controllers\Clients\ClientMnoCredentialController::class)->group(function () {
               Route::get('/credentialsofsmschannel/{id}', 'credentialsofsmschannel');
            });
         //

         //PAYMENTS PROVIDERS
            Route::controller(\App\Http\Controllers\Clients\PaymentsProviderController::class)->group(function () {
               Route::get('/paymentsproviders/findoneby', 'findOneBy');
               Route::put('/paymentsproviders/{id}', 'update');
               Route::get('/paymentsproviders/{id}', 'show');
               Route::post('/paymentsproviders', 'store');
               Route::get('/paymentsproviders', 'index');
            });
         //

         //PAYMENTS PROVIDER Credentials
            Route::controller(\App\Http\Controllers\Clients\PaymentsProviderCredentialController::class)->group(function () {
               Route::get('/paymentsprovidercredentials/findoneby', 'findOneBy');
               Route::put('/paymentsprovidercredentials/{id}', 'update');
               Route::get('/paymentsprovidercredentials/{id}', 'show');
               Route::post('/paymentsprovidercredentials', 'store');
               Route::get('/paymentsprovidercredentials', 'index');
            });
            Route::controller(\App\Http\Controllers\Clients\PaymentsProviderCredentialController::class)->group(function () {
               Route::get('/credentialsofpaymentsprovider/{id}', 'credentialsofpaymentsprovider');
            });
         //  

         //Client Wallets
            Route::controller(\App\Http\Controllers\Clients\ClientWalletController::class)->group(function () {
               Route::get('/clientwallets/findoneby', 'findOneBy');
               Route::put('/clientwallets/{id}', 'update');
               Route::get('/clientwallets/{id}', 'show');
               Route::post('/clientwallets', 'store');
               Route::get('/clientwallets', 'index');
            });
            Route::controller(\App\Http\Controllers\Clients\ClientWalletController::class)->group(function () {
               Route::get('/walletsofclient/{id}', 'walletsofclient');
            });   
         //

         //Client Wallet Credentials
            Route::controller(\App\Http\Controllers\Clients\ClientWalletCredentialController::class)->group(function () {
               Route::get('/walletcredentials/findoneby', 'findOneBy');
               Route::put('/walletcredentials/{id}', 'update');
               Route::get('/walletcredentials/{id}', 'show');
               Route::post('/walletcredentials', 'store');
               Route::get('/walletcredentials', 'index');
            });
            Route::controller(\App\Http\Controllers\Clients\ClientWalletCredentialController::class)->group(function () {
               Route::get('/credentialsofwallet/{id}', 'credentialsofwallet');
            });
         //

         //Aggregated Client
            Route::controller(\App\Http\Controllers\Clients\AggregatedClientController::class)->group(function () {
               Route::get('/aggregatedclients/findoneby', 'findOneBy');
               Route::put('/aggregatedclients/{id}', 'update');
               Route::get('/aggregatedclients/{id}', 'show');
               Route::post('/aggregatedclients', 'store');
               Route::get('/aggregatedclients', 'index');
            });
            Route::controller(\App\Http\Controllers\Clients\AggregatedClientController::class)->group(function () {
               Route::get('/clientsofaggregator/{id}', 'clientsofaggregator');
            });
         //

         //Client Revenue Codes
            Route::controller(\App\Http\Controllers\Clients\ClientRevenueCodeController::class)->group(function () {
               Route::get('/revenuecodes/findoneby', 'findOneBy');
               Route::put('/revenuecodes/{id}', 'update');
               Route::get('/revenuecodes/{id}', 'show');
               Route::post('/revenuecodes', 'store');
               Route::get('/revenuecodes', 'index');
            });
            Route::controller(\App\Http\Controllers\Clients\ClientRevenueCodeController::class)->group(function () {
               Route::get('/revenuecodesofmenu/{menu_id}', 'revenueCodesOfMenu');
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
            Route::get('complainttypesofclient', [\App\Http\Controllers\MenuConfigs\ComplaintTypeController::class, 'index']);
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
         // Surveys
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
         // Sessions 
            Route::controller(\App\Http\Controllers\Sessions\SessionController::class)->group(function () {
               Route::get('/sessions', 'index');
               Route::get('/sessions/{id}', 'show');
               Route::put('/sessions/{id}', 'update');
            });
            Route::get('sessionsofclient', [\App\Http\Controllers\Sessions\SessionofClientController::class, 'index']);

         //

         //Billpay Settings
            Route::controller(\App\Http\Controllers\Auth\BillpaySettingsController::class)->group(function () {
               Route::get('/billpaysettings/findoneby', 'findOneBy');
               Route::put('/billpaysettings/{id}', 'update');
               Route::get('/billpaysettings/{id}', 'show');
               Route::post('/billpaysettings', 'store');
               Route::get('/billpaysettings', 'index');
            });
         //

         // RBAC ROUTES 
            Route::get('usersofclient/{id}', [\App\Http\Controllers\Auth\UsersOfClientController::class, 'index']);      
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
   //
   
});