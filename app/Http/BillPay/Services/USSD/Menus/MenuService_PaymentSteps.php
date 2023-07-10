<?php

namespace App\Http\BillPay\Services\USSD\Menus;

use App\Http\BillPay\DTOs\BaseDTO;
use Illuminate\Pipeline\Pipeline;

class MenuService_PaymentSteps
{

    public function handle(BaseDTO $txDTO)
    {

      try {     
         $txDTO->stepProcessed=false;
         $txDTO = \app(Pipeline::class)
               ->send($txDTO)
               ->through(
                  [
                        \App\Http\BillPay\Services\USSD\Payments\Payments_SubStep_1::class,
                        \App\Http\BillPay\Services\USSD\Payments\Payments_SubStep_2::class,
                        \App\Http\BillPay\Services\USSD\Payments\Payments_SubStep_3::class,
                        \App\Http\BillPay\Services\USSD\Payments\Payments_SubStep_4::class,
                        \App\Http\BillPay\Services\USSD\Payments\Payments_SubStep_5::class
                  ]
               )
               ->thenReturn();
         $txDTO->stepProcessed=false;
      } catch (\Throwable $e) {
         $txDTO->error = 'At payment sub steps. '.$e->getMessage();
         $txDTO->errorType = 'SystemError';
      }
      return $txDTO;
      
   }
}