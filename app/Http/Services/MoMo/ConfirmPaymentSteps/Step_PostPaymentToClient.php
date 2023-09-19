<?php

namespace App\Http\Services\MoMo\ConfirmPaymentSteps;

use App\Http\Services\MoMo\Utility\StepService_AddShotcutMessage;
use App\Http\Services\Contracts\EfectivoPipelineContract;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\BaseDTO;

class Step_PostPaymentToClient extends EfectivoPipelineContract
{

   public function __construct(
      private StepService_AddShotcutMessage $addShotcutMessage)
   {}

   protected function stepProcess(BaseDTO $momoDTO)
   {
      try {
         if($momoDTO->error == ''){
            $momoDTO->stepProcessed = false;
            $momoDTO = app(Pipeline::class)
            ->send($momoDTO)
            ->through(
               [
                  \App\Http\Services\MoMo\BillingClientCallers\PostPaymentLukanga::class,
                  \App\Http\Services\MoMo\BillingClientCallers\PostPaymentSwasco::class,
                  \App\Http\Services\MoMo\BillingClientCallers\PostPaymentMock::class
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