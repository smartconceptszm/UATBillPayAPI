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

      //Complaint Handlers
         $this->app->singleton('Complaint_nkana', function () {
            return $this->app->make(\App\Http\Services\USSD\FaultsComplaints\ClientCallers\Complaint_Local::class);
         });
      //

      //Billing Clients	PostPaid
         $this->app->singleton('nkanaPostPaid', function () {
            return $this->app->make(\App\Http\Services\External\BillingClients\NkanaPostPaid::class);
         });

         $this->app->singleton('ReceiptPostPaidNkana', function () {
            return $this->app->make(\App\Http\Services\External\Adaptors\ReceiptingHandlers\ReceiptPostPaidNkana::class);
         });
      //

      //Billing Clients	PrePaid
         $this->app->singleton('nkanaPrePaid', function () {
            return $this->app->make(\App\Http\Services\External\BillingClients\NkanaPrePaid::class);
         });

         $this->app->singleton('ReceiptPrePaidNkana', function () {
            return $this->app->make(\App\Http\Services\External\Adaptors\ReceiptingHandlers\ReceiptPrePaidNkana::class);
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
