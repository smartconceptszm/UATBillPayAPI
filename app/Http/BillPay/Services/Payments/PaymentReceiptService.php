<?php

namespace App\Http\BillPay\Services\Payments;

use App\Http\BillPay\Services\External\BillingClients\BillingClientBinderService;
use App\Http\BillPay\Services\MoMo\ConfirmPaymentSteps\Step_PostPaymentToClient;
use App\Http\BillPay\Services\MoMo\ConfirmPaymentSteps\Step_SendReceiptViaSMS;
use App\Http\BillPay\Services\MoMo\Utility\Step_UpdateTransaction;
use App\Http\BillPay\Repositories\Payments\PaymentToReviewRepo;
use App\Http\BillPay\Services\MoMo\Utility\Step_LogStatus;
use App\Http\BillPay\Services\Contracts\ICreateService;
use Illuminate\Support\Facades\Auth;
use App\Http\BillPay\DTOs\MoMoDTO;
use Illuminate\Pipeline\Pipeline;
use Exception;

class PaymentReceiptService implements ICreateService
{

   private $bindBillingClientService;
   private $repository;
   private $momoDTO;
   public function __construct(BillingClientBinderService $bindBillingClientService,
      PaymentToReviewRepo $repository, 
      MoMoDTO $momoDTO)
   {
      $this->bindBillingClientService = $bindBillingClientService;
      $this->repository = $repository;
      $this->momoDTO = $momoDTO;
   }

   public function create(array $data):object|null{
      try {
         $thePayment = $this->repository->findById($data['id']);
         $momoDTO = $this->momoDTO->fromArray(\get_object_vars($thePayment));
         if($momoDTO->paymentStatus != 'PAID | NOT RECEIPTED'){
               throw new Exception("Unexpected payment status - ".$momoDTO->paymentStatus);
         }
         if($momoDTO->receiptNumber != ''){
               throw new Exception("Payment appears receipted! Receipt number ".$momoDTO->receiptNumber);
         }
         if($momoDTO->mnoTransactionId == ''){
               throw new Exception("Payment not yet confirmed!");
         }  
         //Bind the Services
               $this->bindBillingClientService->bind($momoDTO->urlPrefix);
         //
         $user = Auth::user(); 
         $momoDTO->user_id = $user->id;
         $momoDTO->error = "";
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
