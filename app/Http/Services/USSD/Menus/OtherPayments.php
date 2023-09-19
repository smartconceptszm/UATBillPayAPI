<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\USSD\Menus\IUSSDMenu;
use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\BaseDTO;
use Exception;

class OtherPayments implements IUSSDMenu
{

   public function handle(BaseDTO $txDTO):BaseDTO
   {

      if ($txDTO->error == '') {
         try {
            $txDTO->stepProcessed=false;
            $txDTO = app(Pipeline::class)
            ->send($txDTO)
            ->through(
               [
                  \App\Http\Services\USSD\OtherPayments\OtherPayment_SubStep_1::class,
                  \App\Http\Services\USSD\OtherPayments\OtherPayment_SubStep_2::class,
                  \App\Http\Services\USSD\OtherPayments\OtherPayment_SubStep_3::class,
                  \App\Http\Services\USSD\OtherPayments\OtherPayment_SubStep_4::class,
                  \App\Http\Services\USSD\OtherPayments\OtherPayment_SubStep_5::class,
                  \App\Http\Services\USSD\OtherPayments\OtherPayment_SubStep_6::class,
                  \App\Http\Services\USSD\OtherPayments\OtherPayment_SubStep_7::class
               ]
            )
            ->thenReturn();
            $txDTO->stepProcessed=false;
         } catch (Exception $e) {
            $txDTO->error='At pay for other services menu. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;

   }
}
