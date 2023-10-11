<?php

namespace App\Providers;

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
            return new \App\Http\Services\External\BillingClients\Lukanga(
                  new \App\Http\Services\Utility\XMLtoArrayParser(),
                  new \App\Http\Services\External\BillingClients\LukangaSoapService(
                              \env('LUKANGA_base_URL').\env('LUKANGA_wsdl_URI'),
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
         $this->app->singleton('PostpaymentLukanga', function () {
            return $this->app->make(\App\Http\Services\MoMo\BillingClientCallers\PostPaymentLukanga::class);
         });
         
      //
      
      //Complaint Handlers
         $this->app->singleton('Complaint_lukanga', function () {
            return $this->app->make(\App\Http\Services\USSD\FaultsComplaints\ClientCallers\Complaint_Local::class);
         });
      //

      //Customer Updates Handlers
         $this->app->singleton('Updates_lukanga', function () {
            return $this->app->make(\App\Http\Services\USSD\UpdateDetails\ClientCallers\UpdateDetails_Local::class);
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
