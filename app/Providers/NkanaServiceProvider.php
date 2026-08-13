<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class NkanaServiceProvider extends ServiceProvider
{

   /**
    * Register any application services.
    */
   public function register(): void
   {

    //   //Complaint Handlers
    //      $this->app->bind('Complaint_nkana', function () {
    //         return $this->app->make(\App\Http\Services\USSD\FaultsComplaints\ClientCallers\Complaint_Local::class);
    //      });
    //   //

     //Complaint Handlers
         $this->app->bind('Complaint_nkana', function () {
            return $this->app->make(\App\Http\Services\USSD\FaultsComplaints\ClientCallers\Complaint_Nkana::class);
         });
      //

      //Billing Clients	PostPaid
         $this->app->bind('nkanaPostPaid', function () {
            return $this->app->make(\App\Http\Services\External\BillingClients\NkanaPostPaid::class);
         });

         $this->app->bind('ReceiptPostPaidNkana', function () {
            return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptPostPaidNkana::class);
         });
      //

      //Billing Clients	PrePaid
         $this->app->bind('nkanaPrePaid', function () {
            return $this->app->make(\App\Http\Services\External\BillingClients\NkanaPrePaid::class);
         });

         $this->app->bind('ReceiptPrePaidNkana', function () {
            return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptPrePaidNkana::class);
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
