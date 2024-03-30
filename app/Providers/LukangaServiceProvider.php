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

      //Billing Clients - POST PAID
         $this->app->singleton('lukangaPostPaid', function () {
            return new \App\Http\Services\External\BillingClients\LukangaPostPaid();
         });

         $this->app->singleton('lukangaPOST-PAIDEnquiry', function () {
            return $this->app->make(\App\Http\Services\ExternalAdaptors\BillingEnquiryHandlers\LukangaPostPaidEnquiry::class);
         });

         $this->app->singleton('ReceiptPostPaidLukanga', function () {
            return $this->app->make(\App\Http\Services\ExternalAdaptors\ReceiptingHandlers\ReceiptPostPaidLukanga::class);
         });

      //

      //Billing Clients - PRE PAID
         $this->app->singleton('lukangaPrePaid', function () {
            return $this->app->make(\App\Http\Services\External\BillingClients\LukangaPrePaid::class,
                              [
                                 'baseURL' => \env('LUKANGA_PREPAID_BASE_URL'),
                                 'platformId' => \env('LUKANGA_PREPAID_PLATFORMID'),
                                 'purchaseEncryptor'=>new \App\Http\Services\External\BillingClients\Lukanga\PurchaseEncryptor()
                              ]
                        );
         });

         $this->app->singleton('lukangaPRE-PAIDEnquiry', function () {
            return $this->app->make(\App\Http\Services\ExternalAdaptors\BillingEnquiryHandlers\LukangaPrePaidEnquiry::class);
         });

         $this->app->singleton('ReceiptPrePaidLukanga', function () {
            return $this->app->make(\App\Http\Services\ExternalAdaptors\ReceiptingHandlers\ReceiptPrePaidLukanga::class);
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
