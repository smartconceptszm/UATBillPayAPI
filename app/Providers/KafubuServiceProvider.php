<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class KafubuServiceProvider extends ServiceProvider
{

   /**
    * Register any application services.
    */
   public function register(): void
   {


      //USSD Menu Option Handlers

      //

      //Billing Clients - POST PAID
         $this->app->bind('kafubuPostPaid', function () {
            return $this->app->make(\App\Http\Services\External\BillingClients\KafubuPostPaid::class);
         });

         $this->app->bind('ReceiptPostPaidKafubu', function () {
            return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptPostPaidKafubu::class);
         });

         $this->app->bind('ReceiptCompositeKAFUBU', function () {
            return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptCompositeKafubu::class);
         });
      //
      
      //Complaint Handlers
         $this->app->bind('Complaint_kafubu', function () {
            return $this->app->make(\App\Http\Services\USSD\FaultsComplaints\ClientCallers\Complaint_Local::class);
         });
      //

      //Customer Updates Handlers
         $this->app->bind('Updates_kafubu', function () {
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
