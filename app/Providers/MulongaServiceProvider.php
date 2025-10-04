<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MulongaServiceProvider extends ServiceProvider
{

   /**
    * Register any application services.
    */
   public function register(): void
   {

      //Billing Clients	PostPaid		
         $this->app->singleton('MulongaPostPaid', function () {
            return $this->app->make(\App\Http\Services\External\BillingClients\MulongaPostPaid::class);
         });

         $this->app->singleton('ReceiptPostPaidMulonga', function () {
            return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptPostPaidMulonga::class);
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
