<?php

namespace App\Http\Services\USSD;

use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\BaseDTO;


class USSDService
{

   public function handle(BaseDTO $txDTO): BaseDTO
   {
   
      // ** Order of the classes in the pipeline is critical!
      $txDTO = \app(Pipeline::class)
         ->send($txDTO)
         ->through(
            [
               \App\Http\Services\USSD\Step_IdentifyClient::class,
               \App\Http\Services\USSD\Step_IdentifyMenu::class,
               \App\Http\Services\USSD\Step_HandleMenu::class,
               \App\Http\Services\USSD\Step_TrimResponse::class,
               \App\Http\Services\USSD\Step_SaveSession::class,
               \App\Http\Services\USSD\Step_HandleErrorResponse::class
            ]
         )
         ->thenReturn();
      //
      return $txDTO;

   }

}

