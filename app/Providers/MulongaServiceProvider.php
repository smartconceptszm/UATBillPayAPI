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
         $this->app->bind('MulongaPostPaid', function () {
            return $this->app->make(\App\Http\Services\External\BillingClients\MulongaPostPaid::class);
         });

         $this->app->bind('ReceiptPostPaidMulonga', function () {
            return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptPostPaidMulonga::class);
         });

         $this->app->bind('ReceiptOtherPaymentsMulonga', function () {
            return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptOtherPaymentsMulonga::class);
         });

         $this->app->bind('ReceiptBulkWaterSalesMulonga', function () {
            return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptBulkWaterSalesMulonga::class);
         });

         $this->app->bind('ReceiptVacuumTankerMulonga', function () {
            return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptVacuumTankerMulonga::class);
         });
         
         $this->app->bind('ReceiptHireWaterBowserMulonga', function () {
            return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptHireWaterBowserMulonga::class);
         });

         $this->app->bind('ReceiptUnblockingSewerMulonga', function () {
            return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptUnblockingSewerMulonga::class);
         });

         $this->app->bind('ReceiptNewWaterConnectionMulonga', function () {
            return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptNewWaterConnectionMulonga::class);
         });

         $this->app->bind('ReceiptNewSewerConnectionMulonga', function () {
            return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptNewSewerConnectionMulonga::class);
         });
         
      //

      //Complaint Handlers
         $this->app->bind('Complaint_mulonga', function () {
            return $this->app->make(\App\Http\Services\USSD\FaultsComplaints\ClientCallers\Complaint_Local::class);
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
