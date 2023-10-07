<?php

namespace App\Providers;


//Faults/Complaints Handlers
use App\Http\Services\USSD\FaultsComplaints\ClientCallers\Complaint_Local;

//Customer Updates Handlers
use App\Http\Services\USSD\UpdateDetails\ClientCallers\UpdateDetails_Local;

//Billing Clients
use App\Http\Services\MoMo\BillingClientCallers\PostPaymentLukanga;
use App\Http\Services\External\BillingClients\LukangaSoapService;
use App\Http\Services\External\BillingClients\Lukanga;

//Utility Services
use App\Http\Services\Utility\XMLtoArrayParser;

//Laravel Dependancies
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class LukangaServiceProvider extends ServiceProvider implements DeferrableProvider
{

   /**
    * Register any application services.
    */
   public function register(): void
   {

      //USSD Menu Option Handlers

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
         $this->app->singleton('PostaymentLukanga', function () {
            return new PostPaymentLukanga(new \App\Http\Services\External\BillingClients\IBillingClient());
         });
         
      //
      
      //Complaint Handlers
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

         'lukanga','Complaint_lukanga','Updates_lukanga','PostaymentLukanga'

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
