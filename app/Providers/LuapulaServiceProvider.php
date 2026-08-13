<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class LuapulaServiceProvider extends ServiceProvider
{

   /**
    * Register any application services.
    */
   public function register(): void
   {


      //USSD Menu Option Handlers
         $this->app->bind('ServiceApplications', function () {
            return $this->app->make(\App\Http\Services\USSD\Menus\ServiceApplications::class);
         });
      //

      //Complaint Handlers
         $this->app->bind('Complaint_luapula', function () {
            return $this->app->make(\App\Http\Services\USSD\FaultsComplaints\ClientCallers\Complaint_Local::class);
         });
      //

      //Customer Updates Handlers
         $this->app->bind('Updates_luapula', function () {
            return $this->app->make(\App\Http\Services\USSD\UpdateDetails\ClientCallers\UpdateDetails_Local::class);
         });
      //

      //Survey Entry Handlers
			$this->app->bind('Survey_luapula', function () {
				return $this->app->make(\App\Http\Services\USSD\Survey\ClientCallers\Survey_Local::class);
			});
		//


      //Service Application Handlers
         $this->app->bind('ServiceApplications_luapula', function () {
            return $this->app->make(\App\Http\Services\USSD\ServiceApplications\ClientCallers\ServiceApplication_Local::class);
         });
      //

      //Billing Clients	PostPaid		
         $this->app->bind('luapulaPostPaid', function () {
            return $this->app->make(\App\Http\Services\External\BillingClients\LuapulaPostPaid::class);
         });

         $this->app->bind('ReceiptPostPaidLuapula', function () {
            return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptPostPaidLuapula::class);
         });
      //

      //Billing Clients	PrePaid
         $this->app->bind('luapulaPrePaid', function () {
            return $this->app->make(\App\Http\Services\External\BillingClients\LuapulaPrePaid::class);
         });

         $this->app->bind('ReceiptPrePaidLuapula', function () {
            return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptPrePaidLuapula::class);
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
