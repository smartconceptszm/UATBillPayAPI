<?php

namespace App\Http\Services\USSD\StepServices;

use App\Http\Services\Clients\ClientRevenuePointService;
use App\Http\Services\Enums\USSDStatusEnum;
use App\Http\Services\Auth\UserService;
use App\Http\DTOs\BaseDTO;

class GetRevenueCollectionDetails
{

	public function __construct(
		private ClientRevenuePointService $clientRevenuePointService,
		private UserService $userService)
	{}

    public function handle(BaseDTO $txDTO):BaseDTO
    {

      try {

         $txDTO->revenueCollector = "POS";
         $txDTO->revenuePoint = "UNKOWN";
         $arrReference = explode("-",$txDTO->reference);
         if(count($arrReference)==2){
            
            $revenuePoint = $this->clientRevenuePointService->findOneBy(['client_id'=>$txDTO->client_id,
                                                                                    'code'=>$arrReference[0]]);
            if($revenuePoint){
               $txDTO->revenuePoint = $revenuePoint->name;
            }

            $revenueColletor = $this->userService->findOneBy(['client_id'=>$txDTO->client_id,
                                                                  'revenueCollectorCode'=>$arrReference[1]]);
            if($revenueColletor){
               $txDTO->revenueCollector = $arrReference[1];
            }

         }
         
      } catch (\Throwable $e) {
         $txDTO->errorType = USSDStatusEnum::SystemError->value;
         $txDTO->error = $e->getMessage();
      }
      
      return $txDTO;
        
    }

    
    
}