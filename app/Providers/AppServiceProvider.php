<?php

namespace App\Providers;

//Faults/Complaints Handlers
use App\Http\BillPay\Services\USSD\FaultsComplaints\ClientCallers\Complaint_Local;
use App\Http\BillPay\Services\USSD\FaultsComplaints\ClientCallers\Complaint_Swasco;

//Customer Updates Handlers
use App\Http\BillPay\Services\USSD\UpdateDetails\ClientCallers\UpdateDetails_Swasco;
use App\Http\BillPay\Services\USSD\UpdateDetails\ClientCallers\UpdateDetails_Local;

//Billing Clients
use App\Http\BillPay\Services\External\BillingClients\LukangaSoapService;
use App\Http\BillPay\Services\External\BillingClients\Lukanga;
use App\Http\BillPay\Services\External\BillingClients\Swasco;

//USSD Error Response Services
use App\Http\BillPay\Services\USSD\ErrorResponses\InvalidConfirmation;
use App\Http\BillPay\Services\USSD\ErrorResponses\MaintenanceMode;
use App\Http\BillPay\Services\USSD\ErrorResponses\InvalidAccount;
use App\Http\BillPay\Services\USSD\ErrorResponses\InvalidAmount;
use App\Http\BillPay\Services\USSD\ErrorResponses\ClientBlocked;
use App\Http\BillPay\Services\USSD\ErrorResponses\InvalidInput;
use App\Http\BillPay\Services\USSD\ErrorResponses\SystemError;
use App\Http\BillPay\Services\USSD\ErrorResponses\NoError;

//USSD Menu Services
use App\Http\BillPay\Services\USSD\Menus\ServiceApplications;
use App\Http\BillPay\Services\USSD\Menus\FaultsComplaints;
use App\Http\BillPay\Services\USSD\Menus\CleanupSession;
use App\Http\BillPay\Services\USSD\Menus\OtherPayments;
use App\Http\BillPay\Services\USSD\Menus\UpdateDetails;
use App\Http\BillPay\Services\USSD\Menus\CheckBalance;
use App\Http\BillPay\Services\USSD\Menus\BuyUnits;
use App\Http\BillPay\Services\USSD\Menus\PayBill;
use App\Http\BillPay\Services\USSD\Menus\Home;

//MoMo Services
use App\Http\BillPay\Services\External\MoMoClients\ZamtelKwacha;
use App\Http\BillPay\Services\External\MoMoClients\AirtelMoney;
use App\Http\BillPay\Services\External\MoMoClients\MoMoMock;
use App\Http\BillPay\Services\External\MoMoClients\MTNMoMo;

//SMS Service Clients
use App\Http\BillPay\Services\External\SMSClients\MTNMoMoDeliverySMS;
use App\Http\BillPay\Services\External\SMSClients\MockDeliverySMS;
use App\Http\BillPay\Services\External\SMSClients\SwascoSMS;
use App\Http\BillPay\Services\External\SMSClients\ZamtelSMS;
use App\Http\BillPay\Services\External\SMSClients\Kannel;


//Utility Services
use App\Http\BillPay\Services\Utility\XMLtoArrayParser;

//Laravel Dependancies
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider implements DeferrableProvider
{

   /**
    * Register any application services.
    */
   public function register(): void
   {

      //USSD Menu Option Handlers
         $this->app->singleton('ServiceApplications', function () {
               return new ServiceApplications(
                  new \App\Http\BillPay\Services\USSD\Menus\MenuService_PaymentSteps()
               );
         });
         $this->app->singleton('FaultsComplaints', function () {
               return new FaultsComplaints(
                  new \App\Http\BillPay\Services\USSD\FaultsComplaints\ClientCallers\ComplaintClientBinderService()
               );
         });
         $this->app->singleton('OtherPayments', function () {
               return new OtherPayments();
         });
         $this->app->singleton('UpdateDetails', function () {
               return new UpdateDetails(
                  new \App\Http\BillPay\Services\USSD\UpdateDetails\ClientCallers\UpdateDetailsClientBinderService()
               );
         });
         $this->app->singleton('CheckBalance', function () {
               return new CheckBalance();
         });
         $this->app->singleton('BuyUnits', function () {
               return new BuyUnits(
                  new \App\Http\BillPay\Services\USSD\Menus\MenuService_PaymentSteps()
               );
         });
         $this->app->singleton('Cleanup', function () {
               return new CleanupSession();
         });
         $this->app->singleton('PayBill', function () {
               return new PayBill(
                  new \App\Http\BillPay\Services\USSD\Menus\MenuService_PaymentSteps()
               );
         });
         $this->app->singleton('Home', function () {
               return new Home();
         });
      //

      //USSD Error Response Handlers
         $this->app->singleton('InvalidConfirmation', function () {
               return new InvalidConfirmation();
         });
         $this->app->singleton('MaintenanceMode', function () {
               return new MaintenanceMode();
         });
         $this->app->singleton('InvalidAccount', function () {
               return new InvalidAccount();
         });
         $this->app->singleton('InvalidAmount', function () {
               return new InvalidAmount();
         });
         $this->app->singleton('ClientBlocked', function () {
               return new ClientBlocked();
         });
         $this->app->singleton('InvalidInput', function () {
               return new InvalidInput();
         });
         $this->app->singleton('SystemError', function () {
               return new SystemError();
         });
         $this->app->singleton('NoError', function () {
               return new NoError();
         });
      //

      //MoMo Clients
      
         $this->app->singleton('MoMoMock', function () {
               return new MoMoMock();
         });
         $this->app->singleton('ZAMTEL', function () {
               return new ZamtelKwacha();
         });
         $this->app->singleton('AIRTEL', function () {
                  return new AirtelMoney();
               });
         $this->app->singleton('MTN', function () {
                  return new MTNMoMo();
               });
      //

      //SMS Clients
         $this->app->singleton('ZAMTELSMS', function () {
               return new ZamtelSMS();
         });
         $this->app->singleton('KANNEL', function () {
               return new Kannel();
         });
         $this->app->singleton('SWASCOSMS', function () {
               return new SwascoSMS(
                              \config('efectivo_clients.swasco.sms_Base_URL'),
                              \config('efectivo_clients.swasco.sms_APIKEY'),
                              \config('efectivo_clients.swasco.sms_SENDER_ID')
                           );
         });
         $this->app->singleton('MTNDeliverySMS', function () {
               return new MTNMoMoDeliverySMS();
         });
         $this->app->singleton('MockDeliverySMS', function () {
               return new MockDeliverySMS();
         });
      //

      //Billing Clients
         $this->app->singleton('lukanga', function () {
               return new Lukanga(
                     new XMLtoArrayParser(),
                     new LukangaSoapService(\env('LUKANGA_base_URL').\env('LUKANGA_wsdl_URI'),
                                          [
                                             'exceptions' => true,
                                             'cache_wsdl' => WSDL_CACHE_BOTH,
                                             'soap_version' => SOAP_1_1,
                                             'trace' => 1,
                                             'connection_timeout' => \env('LUKANGA_soapConnectionTimeout')
                                          ]),
                     \env('LUKANGA_soapUsername'),
                     \env('LUKANGA_soapPassword'),
                     \env('LUKANGA_soapToken'),
                     \env('LUKANGA_soapOperator')
                  );
         });
         
         $this->app->singleton('swasco', function () {
               return new Swasco(                    
                           intval(\config('efectivo_clients.swasco.receipting_Timeout')),
                           intval(\config('efectivo_clients.swasco.remote_Timeout')),
                           \env('SWASCO_base_URL')
                     );
         });
      //
      
      //Complaint Handlers
         $this->app->singleton('Complaint_swasco', function () {
               return new Complaint_Swasco(
                     new Swasco(                    
                           intval(\config('efectivo_clients.swasco.receipting_Timeout')),
                           intval(\config('efectivo_clients.swasco.remote_Timeout')),
                           \config('efectivo_clients.swasco.sms_Base_URL'),
                           \env('SWASCO_base_URL'))
               );
         });
         
         $this->app->singleton('Complaint_lukanga', function () {
               return new Complaint_Local(
                     new \App\Http\BillPay\Services\ComplaintService(
                           new \App\Http\BillPay\Repositories\ComplaintRepo()
                     )
                  );
         });
      //

      //Customer Updates Handlers
         $this->app->singleton('Updates_swasco', function () {
               return new UpdateDetails_Swasco(
                  new \App\Http\BillPay\Services\External\BillingClients\IBillingClient()
               );
         });
         
         $this->app->singleton('Updates_lukanga', function () {
               return new UpdateDetails_Local(
                  new \App\Http\BillPay\Services\CustomerDetailService(
                     new \App\Http\BillPay\Repositories\CustomerDetailRepo())
               );
         });
      //

   }

   /**
   * Get the services provided by the provider.
   *
   * @return array
   */
   public function provides()
   {

      return [
         'ServiceApplications','FaultsComplaints','OtherPayments',
         'UpdateDetails','CheckBalance','BuyUnits','Cleanup',
         'PayBill','Home',
         'InvalidConfirmation','MaintenanceMode','InvalidAccount',
         'InvalidAmount','ClientBlocked','InvalidInput',
         'SystemError','NoError',
         'MoMoMock','ZAMTEL','AIRTEL','MTN',
         'ZAMTELSMS','KANNEL','SWASCOSMS','MTNDeliverySMS',
         'MockDeliverySMS',
         'lukanga','swasco',
         'Complaint_swasco','Complaint_lukanga',
         'Updates_swasco','Updates_lukanga'

      ];

   }

   /**
    * Bootstrap any application services.
    */
   public function boot(): void
   {
      //
   }

}
