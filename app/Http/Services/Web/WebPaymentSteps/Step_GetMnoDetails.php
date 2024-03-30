<?php

namespace App\Http\Services\Web\WebPaymentSteps;

use App\Http\Services\Contracts\EfectivoPipelineContract;
USE App\Http\Services\Clients\MnoService;
use App\Http\DTOs\BaseDTO;
use Exception;

class Step_GetMnoDetails extends EfectivoPipelineContract
{

   public function __construct(
      private MnoService $mnoService)
   {}

   protected function stepProcess(BaseDTO $webDTO)
   {

      try {
         $mno = $this->mnoService->findById($webDTO->mno_id);     
         $mno = \is_null($mno)?null:(object)$mno->toArray();     
         $webDTO->mnoName = $mno->name;
      } catch (\Throwable $e) {
         $webDTO->error = 'At get web MNO details. '.$e->getMessage();
      }
      return $webDTO;

   }
   
}