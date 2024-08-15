<?php

namespace App\Http\Services\USSD;

use App\Http\Services\USSD\StepServices\GetExistingSession;
use App\Http\Services\USSD\StepServices\CreateNewSession;
use App\Http\Services\Contracts\EfectivoPipelineContract;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;
use Exception;

class Step_IdentifyMenu extends EfectivoPipelineContract
{

   public function __construct(
      private GetExistingSession $existingSession,
      private CreateNewSession $newSession)
   {}
   
   protected function stepProcess(BaseDTO $txDTO)
   {

      if($txDTO->error == ''){ 
         try {
            if($txDTO->isNewRequest == '1'){
               $txDTO = $this->newSession->handle($txDTO);
            }else{
               $txDTO = $this->existingSession->handle($txDTO);
            }
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
      }

      App::bind(\App\Http\Services\USSD\Menus\IUSSDMenu::class,$txDTO->handler);
      return $txDTO;
      
   }

}