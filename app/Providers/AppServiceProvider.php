<?php

namespace App\Providers;

//Service Applications Responses Handlers
use App\Http\Services\USSD\ServiceApplications\ClientCallers\ServiceApplication_Local;

//Faults/Complaints Handlers
use App\Http\Services\USSD\FaultsComplaints\ClientCallers\Complaint_Local;
use App\Http\Services\USSD\FaultsComplaints\ClientCallers\Complaint_Swasco;

//Customer Updates Handlers
use App\Http\Services\USSD\UpdateDetails\ClientCallers\UpdateDetails_Swasco;
use App\Http\Services\USSD\UpdateDetails\ClientCallers\UpdateDetails_Local;

//Survey Responses Handlers
use App\Http\Services\USSD\Survey\ClientCallers\Survey_Local;

//Billing Clients
use App\Http\Services\External\BillingClients\LukangaSoapService;
use App\Http\Services\External\BillingClients\Lukanga;
use App\Http\Services\External\BillingClients\Swasco;

//USSD Error Response Services
use App\Http\Services\USSD\ErrorResponses\InvalidConfirmation;
use App\Http\Services\USSD\ErrorResponses\MaintenanceMode;
use App\Http\Services\USSD\ErrorResponses\InvalidAccount;
use App\Http\Services\USSD\ErrorResponses\InvalidAmount;
use App\Http\Services\USSD\ErrorResponses\ClientBlocked;
use App\Http\Services\USSD\ErrorResponses\InvalidInput;
use App\Http\Services\USSD\ErrorResponses\SystemError;
use App\Http\Services\USSD\ErrorResponses\MoMoOffline;
use App\Http\Services\USSD\ErrorResponses\NoError;

//USSD Menu Services
use App\Http\Services\USSD\Menus\ServiceApplications;
use App\Http\Services\USSD\Menus\FaultsComplaints;
use App\Http\Services\USSD\Menus\CleanupSession;
use App\Http\Services\USSD\Menus\OtherPayments;
use App\Http\Services\USSD\Menus\UpdateDetails;
use App\Http\Services\USSD\Menus\CheckBalance;
use App\Http\Services\USSD\Menus\BuyUnits;
use App\Http\Services\USSD\Menus\PayBill;
use App\Http\Services\USSD\Menus\Survey;
use App\Http\Services\USSD\Menus\Home;

//MoMo Services
use App\Http\Services\External\MoMoClients\ZamtelKwacha;
use App\Http\Services\External\MoMoClients\AirtelMoney;
use App\Http\Services\External\MoMoClients\MoMoMock;
use App\Http\Services\External\MoMoClients\MTNMoMo;

//SMS Service Clients
use App\Http\Services\External\SMSClients\MTNMoMoDeliverySMS;
use App\Http\Services\External\SMSClients\MockDeliverySMS;
use App\Http\Services\External\SMSClients\SwascoSMS;
use App\Http\Services\External\SMSClients\ZamtelSMS;
use App\Http\Services\External\SMSClients\Kannel;

//Utility Services
use App\Http\Services\Utility\XMLtoArrayParser;

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
                  new \App\Http\Services\USSD\ServiceApplications\ClientCallers\ServiceApplicationClientBinderService()
               );
         });
         $this->app->singleton('FaultsComplaints', function () {
            return new FaultsComplaints(
                  new \App\Http\Services\USSD\FaultsComplaints\ClientCallers\ComplaintClientBinderService()
               );
         });
         $this->app->singleton('OtherPayments', function () {
            return new OtherPayments();
         });
         $this->app->singleton('UpdateDetails', function () {
            return new UpdateDetails(
                  new \App\Http\Services\USSD\UpdateDetails\ClientCallers\UpdateDetailsClientBinderService()
               );
         });
         $this->app->singleton('CheckBalance', function () {
               return new CheckBalance();
         });
         $this->app->singleton('BuyUnits', function () {
            return new BuyUnits();
         });
         $this->app->singleton('Cleanup', function () {
            return new CleanupSession();
         });
         $this->app->singleton('PayBill', function () {
            return new PayBill();
         });
         $this->app->singleton('Survey', function () {
            return new Survey(
                  new \App\Http\Services\USSD\Survey\ClientCallers\SurveyClientBinderService()
               );
         });
         $this->app->singleton('Home', function () {
            return new Home(new \App\Http\Services\Clients\ClientMenuService(
               new \App\Models\ClientMenu()
            ));
         });
      //

      //USSD Error Response Handlers
         $this->app->singleton('InvalidConfimation', function () {
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
         $this->app->singleton('MoMoOffline', function () {
               return new MoMoOffline();
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
                  new \App\Http\Services\CRM\ComplaintService(
                     new \App\Models\Complaint()
                  )
               );
         });
      //

      //Customer Updates Handlers
         $this->app->singleton('Updates_lukanga', function () {
            return new UpdateDetails_Local(
                        new \App\Http\Services\CRM\CustomerFieldUpdateDetailService(                     
                           new \App\Models\CustomerFieldUpdateDetail()
                        ),
                  new \App\Http\Services\CRM\CustomerFieldUpdateService(
                           new \App\Models\CustomerFieldUpdate()
                        ),
                  new \App\Http\Services\MenuConfigs\CustomerFieldService(
                           new \App\Models\CustomerField()
                        )
               );
         });
         
         $this->app->singleton('Updates_swasco', function () {
               return new UpdateDetails_Swasco(
                     new Swasco(                    
                        intval(\config('efectivo_clients.swasco.receipting_Timeout')),
                        intval(\config('efectivo_clients.swasco.remote_Timeout')),
                        \config('efectivo_clients.swasco.sms_Base_URL'),
                        \env('SWASCO_base_URL'))
                  );
            });
      //

      //Survey Entry Handlers
         $this->app->singleton('Survey_swasco', function () {
               return new Survey_Local(new \App\Http\Services\CRM\SurveyEntryDetailService(new \App\Models\SurveyEntryDetail()),
                  new \App\Http\Services\MenuConfigs\SurveyQuestionService(new \App\Models\SurveyQuestion()),
                  new \App\Http\Services\CRM\SurveyEntryService(new \App\Models\SurveyEntry())
               );
            });
         $this->app->singleton('Survey_lukanga', function () {
            return new Survey_Local(new \App\Http\Services\CRM\SurveyEntryDetailService(new \App\Models\SurveyEntryDetail()),
               new \App\Http\Services\MenuConfigs\SurveyQuestionService(new \App\Models\SurveyQuestion()),
               new \App\Http\Services\CRM\SurveyEntryService(new \App\Models\SurveyEntry())
            );
         });
      //

      //Service Application Handlers
         $this->app->singleton('ServiceApplications_chambeshi', function () {
               return new ServiceApplication_Local(
                     new \App\Http\Services\CRM\ServiceApplicationDetailService(
                        new \App\Models\ServiceApplicationDetail()
                     ),
                     new \App\Http\Services\MenuConfigs\ServiceTypeDetailService(
                        new \App\Models\ServiceTypeDetail()
                     ),
                     new \App\Http\Services\CRM\ServiceApplicationService(
                           new \App\Models\ServiceApplication()
                        )
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
