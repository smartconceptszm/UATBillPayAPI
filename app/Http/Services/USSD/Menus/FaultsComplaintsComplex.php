<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\USSD\Menus\IUSSDMenu;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;

class FaultsComplaintsComplex implements IUSSDMenu
{

   public function handle(BaseDTO $txDTO):BaseDTO
   {
        
      if($txDTO->error=='' ){
         try {
            $stepCount = \count(\explode("*", $txDTO->customerJourney)) -1;
            if ($stepCount == 5) {
               //Bind selected Billing Client to the Interface
                  $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
                  $billingClient = $billpaySettings['USE_BILLING_MOCK_'.strtoupper($txDTO->urlPrefix)]=="YES"? 'MockBillingClient':$txDTO->billingClient;	
					   App::bind(\App\Http\Services\External\BillingClients\IBillingClient::class,$billingClient);	
               //
               //Bind the Complaint Creator Client 
                  App::bind(\App\Http\Services\USSD\FaultsComplaints\ClientCallers\IComplaintClient::class,'Complaint_'.$txDTO->urlPrefix);
               //
            }
            $stepHandler = App::make('FaultsComplaints_Step_'.$stepCount);
            $txDTO = $stepHandler->run($txDTO);
         } catch (\Throwable $e) {
            $txDTO->error = 'At handle faults and complaints menu. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;

   }
    
}