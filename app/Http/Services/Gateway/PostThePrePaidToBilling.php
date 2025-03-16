<?php

namespace App\Http\Services\Gateway;

use App\Http\Services\Clients\ClientMenuService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\BaseDTO;

class PostThePrePaidToBilling
{

   private const USE_RECEIPTING_MOCK_KEY = 'USE_RECEIPTING_MOCK_';
   private const USE_BILLING_MOCK_KEY = 'USE_BILLING_MOCK_';

   public function __construct(
      private ClientMenuService $clientMenuService
   ) {}

   public function handle(BaseDTO $paymentDTO)
   {

      try {

         $billpaySettings = json_decode(cache('billpaySettings', json_encode([])), true);;
         $this->bindHandlers($paymentDTO, $billpaySettings);

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

   private function bindHandlers(BaseDTO $paymentDTO, array $billpaySettings)
   {

      $menu = $this->clientMenuService->findById($paymentDTO->menu_id);
      $this->bindReceiptingAndBillingHandlers($paymentDTO, $billpaySettings, $menu);
   }

   private function bindReceiptingAndBillingHandlers(BaseDTO $paymentDTO, array $billpaySettings, $menu)
   {
      $receiptingHandler = $this->getReceiptingHandler($paymentDTO, $billpaySettings, $menu);
      $billingClient = $this->getBillingClient($paymentDTO, $billpaySettings, $menu);

      App::bind(\App\Http\Services\External\BillingClients\IBillingClient::class, $billingClient);
      App::bind(\App\Http\Services\Gateway\ReceiptingHandlers\IReceiptPayment::class, $receiptingHandler);
   }

   private function getReceiptingHandler(BaseDTO $paymentDTO, array $billpaySettings, $menu): string
   {
      return $billpaySettings[self::USE_RECEIPTING_MOCK_KEY . strtoupper($paymentDTO->urlPrefix)] === "YES"
         ? "MockReceipting"
         : $menu->receiptingHandler;
   }

   private function getBillingClient(BaseDTO $paymentDTO, array $billpaySettings, $menu): string
   {
      return $billpaySettings[self::USE_BILLING_MOCK_KEY . strtoupper($paymentDTO->urlPrefix)] === "YES"
         ? "MockBillingClient"
         : $menu->billingClient;
   }
}