<?php

namespace App\Http\Services\Payments;

use App\Http\Services\Gateway\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\Payments\PaymentToReviewService;
use App\Http\Services\Clients\ClientCustomerService;
use App\Http\DTOs\CompositeReceiptDTO;
use Exception;

class CompositePaymentReceiptService
{

   public function __construct(
      private PaymentToReviewService $paymentToReviewService,
      private ClientCustomerService $clientCustomerService,
      private IReceiptPayment $receiptingService,
      private CompositeReceiptDTO $receiptDTO)
   {}

   public function create(array $data):object|null{
      
      try {

         $customer = $this->clientCustomerService->findOneBy(['customerAccount'=>$data['customerAccount']]);
         $thePayment = $this->paymentToReviewService->findById($data['transaction_id']);
         $receiptDTO = $this->receiptDTO->fromArray($data);
         $receiptDTO->ppTransactionId = $thePayment->ppTransactionId;
         $receiptDTO->walletHandler = $thePayment->walletHandler;
         $receiptDTO->balance = $customer->balance;
         $receiptDTO->payment_id = $thePayment->id;
         //Receipt the payment to the selected child account
         $receiptDTO = $this->receiptingService->handle($receiptDTO);
         //Update the local balance
         $this->clientCustomerService->update([
                                          'balance' => (float)$customer->balance - (float)$data['receiptAmount']],
                                          $customer->id
                                       );
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $receiptDTO;
      
   }

}