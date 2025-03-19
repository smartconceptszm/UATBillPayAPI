<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\USSD\Menus\IUSSDMenu;
use App\Http\Services\Enums\USSDStatusEnum;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;

class CouncilPaymentHistory implements IUSSDMenu
{

   public function handle(BaseDTO $txDTO):BaseDTO
   {

      try {  
         if($txDTO->error==''){   
            $step = \count(\explode("*", $txDTO->customerJourney));
            $stepHandler = App::make('CouncilPaymentHistory_Step_'.$step);
            $txDTO = $stepHandler->run($txDTO);
         }
      } catch (\Throwable $e) {
         $txDTO->error = 'At Council Payment History menu. '.$e->getMessage();
         $txDTO->errorType = USSDStatusEnum::SystemError->value;
      }
      return $txDTO;
      
   }

}