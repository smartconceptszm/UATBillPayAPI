<?php

namespace App\Http\Services\Gateway;

use App\Http\Services\Utility\SCLExternalServiceBinder;
use App\Http\Services\Clients\ClientMenuService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\BaseDTO;

class PostThePrePaidToBilling
{

   public function __construct(
      private SCLExternalServiceBinder $sclExternalServiceBinder,
      private ClientMenuService $clientMenuService
   ) {}

   public function handle(BaseDTO $paymentDTO)
   {

      try {

         // Bind Billing and Receipting Handlers
            $this->sclExternalServiceBinder->bindBillingAndReceipting($paymentDTO->urlPrefix,$paymentDTO->menu_id);
         //

         $paymentDTO = App::make(Pipeline::class)
                           ->send($paymentDTO)
                           ->through([
                              \App\Http\Services\Gateway\PostPrePaidToBilling\Step_PostPaymentToBilling::class,
                              \App\Http\Services\Gateway\PostPrePaidToBilling\Step_SendPrePaidReceiptSMS::class,
                              \App\Http\Services\Gateway\Utility\Step_UpdateTransaction::class,
                              \App\Http\Services\Gateway\Utility\Step_LogStatusAll::class,
                           ])
                           ->thenReturn();
      } catch (\Throwable $e) {
         $paymentDTO->error = 'At get confirm payment pipeline. ' . $e->getMessage();
         Log::error($paymentDTO->error);
      }

      return $paymentDTO;
   }

}