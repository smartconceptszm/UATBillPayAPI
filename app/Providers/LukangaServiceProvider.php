<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class LukangaServiceProvider extends ServiceProvider
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
                              \env('LUKANGA_BASE_URL').\env('LUKANGA_WSDL_URI'),
                                       [
                                          'exceptions' => true,
                                          'cache_wsdl' => WSDL_CACHE_BOTH,
                                          'soap_version' => SOAP_1_1,
                                          'trace' => 1,
                                          'connection_timeout' => \env('LUKANGA_SOAP_CONNECTION_TIMEOUT')
                                       ]),
                              \env('LUKANGA_SOAP_USERNAME'),
                              \env('LUKANGA_SOAP_PASSWORD'),
                              \env('LUKANGA_SOAP_TOKEN'),
                              \env('LUKANGA_SOAP_OPERATOR')
                           );
         });
         $this->app->singleton('ReceiptPaymentLukanga', function () {
            return $this->app->make(\App\Http\Services\MoMo\BillingClientCallers\ReceiptPaymentLukanga::class);
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
    * Bootstrap any application services.
    */
   public function boot(): void
   {
      //
   }

}
