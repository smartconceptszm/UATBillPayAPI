<?php

namespace App\Http\BillPay\Services\MoMo\ConfirmPaymentSteps;

use App\Http\BillPay\Services\MoMo\Utility\StepService_AddShotcutMessage;
use App\Http\BillPay\Services\Contracts\EfectivoPipelineContract;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Http\BillPay\DTOs\BaseDTO;
use Illuminate\Pipeline\Pipeline;

class Step_PostPaymentToClient extends EfectivoPipelineContract
{

   private $addShotcutMessage;
   public function __construct(StepService_AddShotcutMessage $addShotcutMessage)
   {
      $this->addShotcutMessage=$addShotcutMessage;
   }

   protected function stepProcess(BaseDTO $momoDTO)
   {
      try {
         if($momoDTO->error == ''){
               $momoDTO->stepProcessed = false;
               $momoDTO = app(Pipeline::class)
               ->send($momoDTO)
               ->through(
                  [
                     \App\Http\BillPay\Services\MoMo\BillingClientCallers\PostPaymentLukanga::class,
                     \App\Http\BillPay\Services\MoMo\BillingClientCallers\PostPaymentSwasco::class,
                     \App\Http\BillPay\Services\MoMo\BillingClientCallers\PostPaymentMock::class
                  ]
               )
               ->thenReturn();
               $momoDTO->stepProcessed=false;
               if($momoDTO->paymentStatus == 'RECEIPTED'){
                  $customer = \json_decode(Cache::get($momoDTO->urlPrefix.
                                          $momoDTO->accountNumber,\json_encode([])), true);
                  if($customer){
                     if (($momoDTO->menu == 'PayBill' || $momoDTO->menu == 'BuyUnits')
                              && ($momoDTO->mobileNumber == $customer['mobileNumber'])) 
                     {
                           try {
                              $momoDTO = $this->addShotcutMessage->handle($momoDTO);
                           } catch (\Throwable $e) {
                              Log::error('('.$momoDTO->clientCode.'). '.$e->getMessage().
                                 '- Session: '.$momoDTO['sessionId'].' - Phone: '.$momoDTO->mobileNumber);
                           }
                     }
                  }
               }
         }
      } catch (\Throwable $e) {
         $momoDTO->error='At post payment pipeline. '.$e->getMessage();
      }
      return  $momoDTO;
      
   }

}