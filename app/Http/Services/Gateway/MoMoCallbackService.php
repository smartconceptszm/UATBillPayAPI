<?php

namespace App\Http\Services\Gateway;

use App\Http\Services\Payments\PaymentToReviewService;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Enums\PaymentStatusEnum;
USE App\Http\Services\Gateway\ConfirmPayment;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\DTOs\MoMoDTO;
use Exception;

class MoMoCallbackService
{

   public function __construct(
      private PaymentToReviewService $paymentToReviewService, 
      private ClientMenuService $clientMenuService,
      private ConfirmPayment $confirmPayment,
      private MoMoDTO $paymentDTO)
   {}

   public function handleAirtel(array $callbackParams):string{
      
      try {
         
      
         $thePayment = $this->paymentToReviewService->findByTransactionId($callbackParams['id']);
         $paymentDTO = $this->paymentDTO->fromArray(\get_object_vars($thePayment));
         $paymentDTO->ppTransactionId = $callbackParams['airtel_money_id'];

         Log::info("(".$paymentDTO->urlPrefix.") Airtel money callback executed on wallet: ".$paymentDTO->mobileNumber);
         
         //Bind Receipting Handler
            $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
            $theMenu = $this->clientMenuService->findById($paymentDTO->menu_id);
            $receiptingHandler = $theMenu->receiptingHandler;
            $billingClient = $theMenu->billingClient;
            if ($billpaySettings['USE_RECEIPTING_MOCK_'.strtoupper($paymentDTO->urlPrefix)] == "YES"){
               $receiptingHandler = "MockReceipting";
               $billingClient = "MockBillingClient";
            }
            App::bind(\App\Http\Services\External\BillingClients\IBillingClient::class,$billingClient);
            App::bind(\App\Http\Services\Gateway\ReceiptingHandlers\IReceiptPayment::class,$receiptingHandler);
         //

         if($callbackParams['status_code'] == 'TS'){
            $paymentDTO->paymentStatus = PaymentStatusEnum::Paid->value;
            $paymentDTO->error = "";
         }else{
            $paymentDTO->paymentStatus = PaymentStatusEnum::Payment_Failed->value;
            $paymentDTO->error = $callbackParams['message'];
         }

         $paymentDTO = $this->confirmPayment->handle($paymentDTO);
   
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return "Handled";
      
   }

}
