<?php

namespace App\Http\Services\USSD\Sessions\NewSession;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Clients\ClientMenuService;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;
use Exception;

class ShortcutSession extends EfectivoPipelineContract
{

   public function __construct(
      private ClientMenuService $clientMenuService)
   {}


   protected function stepProcess(BaseDTO $txDTO)
   {

      try {

         if(($txDTO->isNewRequest == '1') && (\count(\explode("*", $txDTO->subscriberInput))>1)){
            

            $arrInputs = explode("*", $txDTO->subscriberInput);
            $selectedMenu = $this->clientMenuService->findOneBy([
                                                                  'order' => $arrInputs[1],
                                                                  'client_id' => $txDTO->client_id,
                                                                  'parent_id' => $txDTO->menu_id,
                                                                  'isActive' => 'YES'
                                                               ]);
            if($selectedMenu && $selectedMenu->shortcut){
               $shortCut = App::make($selectedMenu->shortcut);
               $txDTO = $shortCut->handle($txDTO,$selectedMenu);
            }else{
               $txDTO->subscriberInput = $txDTO->shortCode;
            }
            return $txDTO;

         }

      } catch (\Throwable $e) {
         throw $e;
      }

      return $txDTO;
      
   }

}