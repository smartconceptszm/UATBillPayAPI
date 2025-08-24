<?php

namespace App\Http\Services\Gateway;

use App\Http\Services\Utility\SCLExternalServiceBinder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\BaseDTO;

class ReConfirmPayment
{
   
   public function __construct(
      private SCLExternalServiceBinder $sclExternalServiceBinder
   ) {}

   public function handle(BaseDTO $paymentDTO)
   {
      try {

         // Bind Billing and Receipting Handlers and Wallet 
            $this->sclExternalServiceBinder->bindAll(
                  $paymentDTO->urlPrefix,$paymentDTO->menu_id,$paymentDTO->walletHandler);
         //

         $paymentDTO = App::make(Pipeline::class)
                           ->send($paymentDTO)
                           ->through([
                              
                              \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_GetPaymentStatus::class,

                              \App\Http\Services\Gateway\Utility\Step_UpdateTransaction::class,
                              \App\Http\Services\Gateway\Utility\Step_LogStatusInfoOnly::class,

                              \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_CheckReceiptStatus::class,
                              \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_PostPaymentToClient::class,

                              \App\Http\Services\Gateway\Utility\Step_UpdateTransaction::class,
                              \App\Http\Services\Gateway\Utility\Step_LogStatusInfoOnly::class,

                              \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_AddShortcutMessageToReceipt::class,
                              \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_AddPromoMessageToReceipt::class,
                              \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_SendReceiptViaSMS::class,

                              \App\Http\Services\Gateway\Utility\Step_UpdateTransaction::class,
                              \App\Http\Services\Gateway\Utility\Step_LogStatusAll::class,

                              \App\Http\Services\Gateway\Utility\Step_RefreshAnalytics::class,
                              \App\Http\Services\Gateway\Utility\Step_AddCutomerToShortcutList::class
                           ])
                           ->thenReturn();
      } catch (\Throwable $e) {
         $paymentDTO->error = 'At get confirm payment pipeline. ' . $e->getMessage();
         Log::info($paymentDTO->error);
      }

      return $paymentDTO;
   }

}