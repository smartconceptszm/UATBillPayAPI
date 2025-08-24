<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\Utility\SCLExternalServiceBinder;
use App\Http\Services\USSD\Menus\IUSSDMenu;
use App\Http\Services\Enums\USSDStatusEnum;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;

class FaultsComplaintsComplex implements IUSSDMenu
{

   public function __construct(
      private SCLExternalServiceBinder $sclExternalServiceBinder
   ) {}

   public function handle(BaseDTO $txDTO):BaseDTO
   {
        
      try {
         if($txDTO->error==''){
            $stepCount = \count(\explode("*", $txDTO->customerJourney)) -1;
            if ($stepCount == 5) {
               //Bind selected Billing Client to the Interface
                  $this->sclExternalServiceBinder->bindBillingClient($txDTO->urlPrefix,$txDTO->menu_id);
               //
               //Bind the Complaint Creator Client 
                  App::bind(\App\Http\Services\USSD\FaultsComplaints\ClientCallers\IComplaintClient::class,'Complaint_'.$txDTO->urlPrefix);
               //
            }
            $stepHandler = App::make('FaultsComplaints_Step_'.$stepCount);
            $txDTO = $stepHandler->run($txDTO);
         }
      } catch (\Throwable $e) {
         $txDTO->error = 'At handle faults and complaints menu. '.$e->getMessage();
         $txDTO->errorType = USSDStatusEnum::SystemError->value;
      }
      return $txDTO;
      
   }
    
}