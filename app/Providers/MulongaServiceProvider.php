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

         $this->app->singleton('ReceiptBulkWaterSalesMulonga', function () {
            return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptBulkWaterSalesMulonga::class);
         });

         $this->app->singleton('ReceiptVacuumTankerMulonga', function () {
            return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptVacuumTankerMulonga::class);
         });
         
         $this->app->singleton('ReceiptHireWaterBowserMulonga', function () {
            return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptHireWaterBowserMulonga::class);
         });

         $this->app->singleton('ReceiptUnblockingSewerMulonga', function () {
            return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptUnblockingSewerMulonga::class);
         });

         $this->app->singleton('ReceiptNewWaterConnectionMulonga', function () {
            return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptNewWaterConnectionMulonga::class);
         });

         $this->app->singleton('ReceiptNewSewerConnectionMulonga', function () {
            return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptNewSewerConnectionMulonga::class);
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
