<?php

namespace App\Http\Services\USSD\FaultsComplaints;

use App\Http\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\Services\MenuConfigs\ComplaintTypeService;
use App\Http\DTOs\BaseDTO;
use Exception;

class FaultsComplaints_SubStep_1 extends EfectivoPipelineWithBreakContract
{

   public function __construct(
      private ComplaintTypeService $complaintType)
   {}

   protected function stepProcess(BaseDTO $txDTO)
   {

      if(\count(\explode("*", $txDTO->customerJourney))==1){
         $txDTO->stepProcessed=true;
         try {
            $arrComplaintTypes = $this->complaintType->findAll(['client_id'=>$txDTO->client_id]);
            $stringMenu = "Select Complaint/Fault Type:\n";
            foreach ($arrComplaintTypes as $complaintType) {
               $stringMenu.=$complaintType->order.'. '.$complaintType->name."\n";
            }
            $txDTO->response=$stringMenu; 
         } catch (Exception $e) {
            $txDTO->error='At complaints step 1. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;

   }

}