<?php

namespace App\Http\Services\USSD\Sessions;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use Illuminate\Support\Facades\App;
use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\BaseDTO;

class Step_ExistingSession extends EfectivoPipelineContract
{

   protected function stepProcess(BaseDTO $txDTO)
   {

      try {

         if($txDTO->isNewRequest != '1'){
            $txDTO = App::make(Pipeline::class)
                        ->send($txDTO)
                        ->through(
                           [
                              \App\Http\Services\USSD\Sessions\ExistingSession\RetrieveSession::class,
                              \App\Http\Services\USSD\Sessions\ExistingSession\RetrieveCurrentMenu::class,
                              \App\Http\Services\USSD\Sessions\ExistingSession\HandleBackStep::class
                           ]
                        )
                        ->thenReturn();
         }

      } catch (\Throwable $e) {
         throw $e;
      }

      return $txDTO;
      
   }

}