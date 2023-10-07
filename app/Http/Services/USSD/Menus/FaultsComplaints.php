<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\USSD\Menus\IUSSDMenu;
use Illuminate\Support\Facades\App;
use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\BaseDTO;
use Exception;

class FaultsComplaints implements IUSSDMenu
{

   public function handle(BaseDTO $txDTO):BaseDTO
   {
        
      if($txDTO->error=='' ){
         try {
            if (\count(\explode("*", $txDTO->customerJourney)) == 5) {
               //Bind selected Billing Client to the Interface
                  App::bind(\App\Http\Services\External\BillingClients\IBillingClient::class,$txDTO->urlPrefix);
               //
               //Bind the Complaint Creator Client 
                  App::bind(\App\Http\Services\USSD\FaultsComplaints\ClientCallers\IComplaintClient::class,'Complaint_'.$txDTO->urlPrefix);
               //
            }
            $stepHandler = App::make('FaultsComplaints_Step_'.\count(\explode("*", $txDTO->customerJourney)));
            $txDTO = $stepHandler->run($txDTO);
         } catch (Exception $e) {
            $txDTO->error = 'At handle faults and complaints menu. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;

   }
    
}