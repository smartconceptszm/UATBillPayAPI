<?php

namespace App\Http\BillPay\Services\Payments;

use App\Http\BillPay\Services\MoMo\ConfirmPaymentSteps\Step_PostPaymentToClient;
use App\Http\BillPay\Services\MoMo\ConfirmPaymentSteps\Step_SendReceiptViaSMS;
use App\Http\BillPay\Services\MoMo\Utility\Step_UpdateTransaction;
use App\Http\BillPay\Services\External\BindExternalServices;
use App\Http\BillPay\Services\MoMo\Utility\Step_LogStatus;
use App\Http\BillPay\Services\Contracts\IUpdateService;
use App\Http\BillPay\Repositories\Payments\PaymentToReviewRepo;
use Illuminate\Pipeline\Pipeline;
use Exception;

class PaymentNotReceiptedService implements IUpdateService
{

   private $bindExternalServices;
   private $repository;
   public function __construct(PaymentToReviewRepo $repository, 
         BindExternalServices $bindExternalServices
   ){
      $this->bindExternalServices=$bindExternalServices;
      $this->repository = $repository;
   }

   public function update(array $data, string $id):object|null{
      try {
         $momoDTO=$this->repository->findById($id);
         if($momoDTO->paymentStatus!='PAID | NOT RECEIPTED'){
               throw new Exception("Unexpected payment status - ".$momoDTO->paymentStatus);
         }
         if($momoDTO->receiptNumber!=''){
               throw new Exception("Payment appears receipted! Receipt number ".$momoDTO->receiptNumber);
         }
         if($momoDTO->mnoTransactionId!=''){
               throw new Exception("Payment not yet confirmed!");
         }  
         //Bind the Services
               $this->bindExternalServices->billingClient($momoDTO);
         //
         $momoDTO =  app(Pipeline::class)
                  ->send($momoDTO)
                  ->through(
                     [
                           Step_PostPaymentToClient::class,
                           Step_SendReceiptViaSMS::class,
                           Step_UpdateTransaction::class,  
                           Step_LogStatus::class 
                     ]
                  )
                  ->thenReturn();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $momoDTO;
   }

}
