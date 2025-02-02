<?php

namespace App\Http\Services\USSD\Sessions\NewSession;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\USSD\StepServices\GetShortcutMenu;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;
use Exception;

class ShortcutSession extends EfectivoPipelineContract
{

   public function __construct(
      private GetShortcutMenu $getShortcutMenu)
   {}


   protected function stepProcess(BaseDTO $txDTO)
   {

      try {

         if(($txDTO->isNewRequest == '1') && (\count(\explode("*", $txDTO->subscriberInput))>1)){
            
            $selectedMenu = $this->getShortcutMenu->handle($txDTO);
            if($selectedMenu){
               $shortcutHandler = App::make($selectedMenu->shortcutHandler);
               $txDTO = $shortcutHandler->handle($txDTO,$selectedMenu);
            }else{
               $txDTO->subscriberInput = $txDTO->shortCode;
            }

         }

      } catch (\Throwable $e) {
         throw $e;
      }

      return $txDTO;
      
   }

}