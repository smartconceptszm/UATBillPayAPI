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

      //USSD Menu Option Handlers
         $this->app->singleton('ServiceApplications', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\ServiceApplications::class);
         });
      //

      //Complaint Handlers
         $this->app->singleton('Complaint_nkana', function () {
            return $this->app->make(\App\Http\Services\USSD\FaultsComplaints\ClientCallers\Complaint_Local::class);
         });
      //

      //Customer Updates Handlers
         $this->app->singleton('Updates_nkana', function () {
            return $this->app->make(\App\Http\Services\USSD\UpdateDetails\ClientCallers\UpdateDetails_Local::class);
         });
      //

      //Service Application Handlers
         $this->app->singleton('ServiceApplications_nkana', function () {
            return $this->app->make(\App\Http\Services\USSD\ServiceApplications\ClientCallers\ServiceApplication_Local::class);
         });
      //

      //Billing Clients	PostPaid
         $this->app->singleton('nkanaPostPaidEnquiry', function () {
            return $this->app->make(\App\Http\Services\ExternalAdaptors\BillingEnquiryHandlers\NkanaPostPaidEnquiry::class);
         });

         $this->app->singleton('ReceiptPostPaidNkana', function () {
            return $this->app->make(\App\Http\Services\ExternalAdaptors\ReceiptingHandlers\ReceiptPostPaidNkana::class);
         });
      //

      //Billing Clients	PrePaid
         $this->app->singleton('nkanaPrePaidEnquiry', function () {
            return $this->app->make(\App\Http\Services\ExternalAdaptors\BillingEnquiryHandlers\NkanaPrePaidEnquiry::class);
         });

         $this->app->singleton('ReceiptPrePaidNkana', function () {
            return $this->app->make(\App\Http\Services\ExternalAdaptors\ReceiptingHandlers\ReceiptPrePaidNkana::class);
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
