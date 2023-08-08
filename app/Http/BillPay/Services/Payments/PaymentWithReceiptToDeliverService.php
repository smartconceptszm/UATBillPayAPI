<?php

namespace App\Http\BillPay\Services\Payments;

use App\Http\BillPay\Services\MoMo\ConfirmPaymentSteps\Step_SendReceiptViaSMS;
use App\Http\BillPay\Services\External\SMSClients\SMSClientBinderService;
use App\Http\BillPay\Services\MoMo\Utility\Step_UpdateTransaction;
use App\Http\BillPay\Repositories\Payments\PaymentToReviewRepo;
use App\Http\BillPay\Services\MoMo\Utility\Step_LogStatus;
use App\Http\BillPay\Services\Contracts\IUpdateService;
use Illuminate\Support\Facades\Auth;
use App\Http\BillPay\DTOs\MoMoDTO;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Carbon;
use Exception;

class PaymentWithReceiptToDeliverService implements IUpdateService
{

   private $smsClientBinderService;
   private $repository;
   private $momoDTO;
   public function __construct(PaymentToReviewRepo $repository,
      SMSClientBinderService $smsClientBinderService,
      MoMoDTO $momoDTO)
   {
      $this->smsClientBinderService = $smsClientBinderService;
      $this->repository=$repository;
      $this->momoDTO = $momoDTO;
   }

   public function update(array $data, string $id):object|null{
      try {
         $thePayment = $this->repository->findById($id);
         $momoDTO = $this->momoDTO->fromArray(\get_object_vars($thePayment));
         if ($momoDTO->paymentStatus == 'RECEIPTED' || $momoDTO->paymentStatus == 'RECEIPT DELIVERED' ) {  
            if(!$momoDTO->receipt){
               //consider a format receipt public method for each billing client
               $momoDTO->receipt = "Payment successful\n" .
                  "Rcpt No.: " . $momoDTO->receiptNumber . "\n" .
                  "Amount: ZMW " . \number_format($momoDTO->receiptAmount, 2, '.', ',') . "\n".
                  "Acc: " . $momoDTO->accountNumber . "\n";
               $momoDTO->receipt.="Date: " . Carbon::now()->format('d-M-Y') . "\n";
            }
            //Bind the SMS Client
               $smsClient = '';
               if(!$smsClient && (\env('SMS_SEND_USE_MOCK')=="YES")){
                     $smsClient = 'MockDeliverySMS';
               }
               if(!$smsClient && \env($this->momoDTO->mnoName.'_HAS_FREESMS')=="YES"){
                     $smsClient = $this->momoDTO->mnoName.'DeliverySMS';
               }
               if(!$smsClient && \config('efectivo_clients.'.$this->momoDTO->urlPrefix.'.hasOwnSMS')){
                     $smsClient = \strtoupper($this->momoDTO->urlPrefix).'SMS';
               }
               if(!$smsClient){
                     $smsClient = \env('SMPP_CHANNEL');
               }
               $this->smsClientBinderService->bind($smsClient);
            //
            $user = Auth::user(); 
            $momoDTO->user_id = $user->id;
            $momoDTO =  app(Pipeline::class)
               ->send($momoDTO)
               ->through(
                     [
                        Step_SendReceiptViaSMS::class,
                        Step_UpdateTransaction::class,  
                        Step_LogStatus::class 
                     ]
               )
               ->thenReturn();
         }
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $momoDTO;
   }
}
