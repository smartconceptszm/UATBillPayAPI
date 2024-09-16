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
            return $this->app->make(\App\Http\Services\External\BillingClients\LukangaPostPaid::class);
         });

         $this->app->singleton('ReceiptPostPaidLukanga', function () {
            return $this->app->make(\App\Http\Services\External\ReceiptingHandlers\ReceiptPostPaidLukanga::class);
         });

      //

      //Billing Clients - PRE PAID
         $this->app->singleton('lukangaPrePaid', function () {
            return $this->app->make(\App\Http\Services\External\BillingClients\LukangaPrePaid::class);
         });

         $this->app->singleton('ReceiptPrePaidLukanga', function () {
            return $this->app->make(\App\Http\Services\External\ReceiptingHandlers\ReceiptPrePaidLukanga::class);
         });
         
      //
      
      //Complaint Handlers
         $this->app->singleton('Complaint_lukanga', function () {
            return $this->app->make(\App\Http\Services\USSD\FaultsComplaints\ClientCallers\Complaint_Local::class);
         });
      //

      //Customer Updates Handlers
         $this->app->singleton('UpdateDetails_lukanga', function () {
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
