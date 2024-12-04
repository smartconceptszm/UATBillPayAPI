<?php

namespace App\Http\Services\USSD\Sessions;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use Illuminate\Support\Facades\App;
use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\BaseDTO;

class Step_NewSession extends EfectivoPipelineContract
{


   protected function stepProcess(BaseDTO $txDTO)
   {

      try {

         $txDTO = App::make(Pipeline::class)
                        ->send($txDTO)
                        ->through(
                           [
                              \App\Http\Services\USSD\Sessions\NewSession\RedirectedSession::class,
                              \App\Http\Services\USSD\Sessions\NewSession\CreateSession::class,
                              \App\Http\Services\USSD\Sessions\NewSession\ResumingSession::class,
                              \App\Http\Services\USSD\Sessions\NewSession\GetAggregatedClient::class,
                              \App\Http\Services\USSD\Sessions\NewSession\ShortcutSession::class
                           ]
                        )
                        ->thenReturn();

      } catch (\Throwable $e) {
         throw $e;
      }

      return $txDTO;
      
   }

}