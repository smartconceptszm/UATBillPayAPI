<?php

namespace App\Http\Services\USSD\FaultsComplaints;

use App\Http\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\Services\MenuConfigs\ComplaintSubTypeService;
use App\Http\Services\MenuConfigs\ComplaintTypeService;
use App\Http\DTOs\BaseDTO;
use Exception;

class FaultsComplaints_SubStep_2 extends EfectivoPipelineWithBreakContract
{

   public function __construct(
      private ComplaintSubTypeService $complaintSubType,
      private ComplaintTypeService $complaintType)
   {}

   protected function stepProcess(BaseDTO $txDTO)
   {
 
      if(\count(\explode("*", $txDTO->customerJourney)) == 2){
         $txDTO->stepProcessed=true;
         try {

            $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
            $theComplaintType = $this->complaintType->findOneBy([
                        'order'=>$txDTO->subscriberInput,
                        'client_id'=>$txDTO->client_id,
                  ]);

            if($theComplaintType->id){
               $subTypes = $this->complaintSubType->findAll([
                              'complaint_type_id'=>$theComplaintType->id
                           ]);
               $stringMenu = $theComplaintType->name." Complaints. Enter:\n";
               foreach ($subTypes as $value){
                  $stringMenu.=$value->order.'. '.$value->name."\n";
               }
               $txDTO->response = $stringMenu;
            }else{
               throw new Exception("Complaint sub types not found", 1);
            }
         } catch (Exception $e) {
            if($e->getCode() == 1){
               $txDTO->errorType = "InvalidInput";
            }else{
               $txDTO->errorType = 'SystemError';
            }
            $txDTO->error='At complaints step 2. '.$e->getMessage();
         }
      }
      return $txDTO;

   }

}