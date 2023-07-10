<?php

namespace App\Http\BillPay\Services\Contracts;

use App\Http\BillPay\DTOs\BaseDTO;
use Closure;


abstract class EfectivoPipelineWithBreakContract
{
   
   public function handle(BaseDTO $txDTO, Closure $next)
   {
      if($txDTO->stepProcessed){
         return $txDTO;
      }
      return $next($this->stepProcess($txDTO));
   }

   protected abstract function stepProcess(BaseDTO $txDTO);

}