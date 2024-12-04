<?php

namespace App\Http\Services\USSD;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use Illuminate\Support\Facades\App;
use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\BaseDTO;

class Step_GetMenu extends EfectivoPipelineContract
{

   protected function stepProcess(BaseDTO $txDTO)
   {

      try {

         $txDTO = App::make(Pipeline::class)
            ->send($txDTO)
            ->through(
               [
                  \App\Http\Services\USSD\Sessions\Step_NewSession::class,
                  \App\Http\Services\USSD\Sessions\Step_ExistingSession::class
               ]
            )
            ->thenReturn();

      } catch (\Throwable $e) {
         switch ($e->getCode()) {

            case 1:
               $txDTO->error = $e->getMessage();
               $txDTO->errorType = 'InvalidInput';
               break;
            case 2:
               $txDTO->error = $e->getMessage();
               $txDTO->errorType = 'MoMoNotActivated';
               break;
            default:
               $txDTO->error = $e->getMessage();
               $txDTO->errorType = 'SystemError';
               break;
         }
         $txDTO->handler = 'DummyMenu';
      }

      App::bind(\App\Http\Services\USSD\Menus\IUSSDMenu::class,$txDTO->handler);
      return $txDTO;
      
   }

}