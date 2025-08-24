<?php

namespace App\Http\Services\Payments;

use App\Http\Services\Gateway\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\Utility\CompositeReceiptingBinderService;
use App\Http\Services\Payments\CompositeReceiptService;
use App\Http\Services\Payments\PaymentToReviewService;
use App\Http\Services\Clients\ClientCustomerService;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Http\DTOs\CompositeReceiptDTO;
use Illuminate\Support\Facades\App;
use Exception;

class CompositePaymentReceiptFailedService
{

   public function __construct(
      private CompositeReceiptService $compositeReceiptService,
      private CompositeReceiptingBinderService $serviceBinder,
      private PaymentToReviewService $paymentToReviewService,
      private ClientCustomerService $clientCustomerService,
      private CompositeReceiptDTO $receiptDTO)
   {}

   public function update(string $id){
      
      try {

         $allocationRecord = $this->compositeReceiptService->findById($id);
         if($allocationRecord->status == PaymentStatusEnum::Paid->value || 
                  $allocationRecord->status == PaymentStatusEnum::NoToken->value )
         {
            $receiptDTO = $this->receiptDTO->fromArray(\get_object_vars($allocationRecord));
            $thePayment = $this->paymentToReviewService->findById($receiptDTO->payment_id);
            $customer =  $this->clientCustomerService->findOneBy([
                                                      'customerAccount'=> $receiptDTO->customerAccount,
                                                      'client_id'=> $receiptDTO->client_id
                                                   ]);
            $receiptDTO->ppTransactionId = $thePayment->ppTransactionId;
            $receiptDTO->walletHandler = $thePayment->walletHandler;
            $receiptDTO->menu_id = $thePayment->menu_id;
            $receiptDTO->balance = $customer->balance;
            // Setup service bindings FIRST
            $this->serviceBinder->bind($thePayment->urlPrefix,$thePayment->menu_id);

            // Now resolve the service (after bindings are set)
            $receiptHandlerService = App::make(IReceiptPayment::class);
            
            // Process the receipt
            $receiptHandlerService->handle($receiptDTO);
            
         }

      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $receiptDTO;
      
   }

}