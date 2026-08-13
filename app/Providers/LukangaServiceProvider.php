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
         $this->app->bind('lukangaPostPaid', function () {
            return $this->app->make(\App\Http\Services\External\BillingClients\LukangaPostPaid::class);
         });

         $this->app->bind('ReceiptPostPaidLukanga', function () {
            return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptPostPaidLukanga::class);
         });

      //

      //Billing Clients - PRE PAID
         $this->app->bind('lukangaPrePaid', function () {
            return $this->app->make(\App\Http\Services\External\BillingClients\LukangaPrePaid::class);
         });

         $this->app->bind('ReceiptPrePaidLukanga', function () {
            return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptPrePaidLukanga::class);
         });
         
      //
      
      //Complaint Handlers
         $this->app->bind('Complaint_lukanga', function () {
            return $this->app->make(\App\Http\Services\USSD\FaultsComplaints\ClientCallers\Complaint_Local::class);
         });
      //

      //Customer Updates Handlers
         $this->app->bind('UpdateDetails_lukanga', function () {
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
