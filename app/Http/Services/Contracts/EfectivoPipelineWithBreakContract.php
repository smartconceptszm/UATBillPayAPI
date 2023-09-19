<?php

namespace App\Http\Services\Contracts;

use App\Http\DTOs\BaseDTO;
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