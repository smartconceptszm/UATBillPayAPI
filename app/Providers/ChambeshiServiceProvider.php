<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ChambeshiServiceProvider extends ServiceProvider
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
         $this->app->singleton('Complaint_chambeshi', function () {
            return $this->app->make(\App\Http\Services\USSD\FaultsComplaints\ClientCallers\Complaint_Local::class);
         });
      //

      //Customer Updates Handlers
         $this->app->singleton('Updates_chambeshi', function () {
            return $this->app->make(\App\Http\Services\USSD\UpdateDetails\ClientCallers\UpdateDetails_Local::class);
         });
      //

      //Survey Entry Handlers
			$this->app->singleton('Survey_chambeshi', function () {
				return $this->app->make(\App\Http\Services\USSD\Survey\ClientCallers\Survey_Local::class);
			});
		//


      //Service Application Handlers
         $this->app->singleton('ServiceApplications_chambeshi', function () {
            return $this->app->make(\App\Http\Services\USSD\ServiceApplications\ClientCallers\ServiceApplication_Local::class);
         });
      //

      //Billing Clients	PostPaid		
         $this->app->singleton('chambeshiPostPaid', function () {
            return $this->app->make(\App\Http\Services\External\BillingClients\ChambeshiPostPaid::class);
         });

         $this->app->singleton('ReceiptPostPaidChambeshi', function () {
            return $this->app->make(\App\Http\Services\External\Adaptors\ReceiptingHandlers\ReceiptPostPaidChambeshi::class);
         });
      //

      //Billing Clients	PrePaid
         $this->app->singleton('chambeshiPrePaid', function () {
            return $this->app->make(\App\Http\Services\External\BillingClients\ChambeshiPrePaid::class);
         });

         $this->app->singleton('ReceiptPrePaidChambeshi', function () {
            return $this->app->make(\App\Http\Services\External\Adaptors\ReceiptingHandlers\ReceiptPrePaidChambeshi::class);
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
