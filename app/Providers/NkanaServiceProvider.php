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

		//Menu Update Mobile Number 
         $this->app->singleton('NkanaOtherPayments', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\NkanaOtherPayments::class);
         });
         //Menu Step Handlers
            $this->app->singleton('NkanaOtherPayments_Step_2', function () {
               return $this->app->make(\App\Http\Services\USSD\NkanaOtherPayments\NkanaOtherPayments_Step_2::class);
            });
            $this->app->singleton('NkanaOtherPayments_Step_4', function () {
               return $this->app->make(\App\Http\Services\USSD\NkanaOtherPayments\NkanaOtherPayments_Step_4::class);
            });
            $this->app->singleton('NkanaOtherPayments_Step_5', function () {
               return $this->app->make(\App\Http\Services\USSD\NkanaOtherPayments\NkanaOtherPayments_Step_5::class);
            });
            $this->app->singleton('NkanaOtherPayments_Step_6', function () {
               return $this->app->make(\App\Http\Services\USSD\NkanaOtherPayments\NkanaOtherPayments_Step_6::class);
            });
         //
      //

      //Complaint Handlers
         $this->app->singleton('Complaint_nkana', function () {
            return $this->app->make(\App\Http\Services\USSD\FaultsComplaints\ClientCallers\Complaint_Local::class);
         });
      //

      //Billing Clients	PostPaid
         $this->app->singleton('nkanaPostPaidEnquiry', function () {
            return $this->app->make(\App\Http\Services\External\Adaptors\BillingEnquiryHandlers\NkanaPostPaidEnquiry::class);
         });

         $this->app->singleton('ReceiptPostPaidNkana', function () {
            return $this->app->make(\App\Http\Services\External\Adaptors\ReceiptingHandlers\ReceiptPostPaidNkana::class);
         });
      //

      //Billing Clients	PrePaid
         $this->app->singleton('nkanaPrePaidEnquiry', function () {
            return $this->app->make(\App\Http\Services\External\Adaptors\BillingEnquiryHandlers\NkanaPrePaidEnquiry::class);
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
