<?php

namespace App\Http\BillPay\Services\USSD\FaultsComplaints;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\MenuConfigs\ComplaintTypeService;
use App\Http\BillPay\DTOs\BaseDTO;

class FaultsComplaints_SubStep_1 extends EfectivoPipelineWithBreakContract
{

   private $complaintType;
   public function __construct(ComplaintTypeService $complaintType)
   {
      $this->complaintType=$complaintType;
   }

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
         } catch (\Throwable $e) {
               $txDTO->error='At Retrieving complaint types. '.$e->getMessage();
               $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;

   }

}