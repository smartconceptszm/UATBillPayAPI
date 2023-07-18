<?php

namespace App\Http\BillPay\Services\USSD\FaultsComplaints;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\MenuConfigs\ComplaintSubTypeService;
use App\Http\BillPay\Services\MenuConfigs\ComplaintTypeService;
use App\Http\BillPay\DTOs\BaseDTO;
use Exception;

class FaultsComplaints_SubStep_2 extends EfectivoPipelineWithBreakContract
{

   private $complaintSubType;
   private $complaintType;
   public function __construct(ComplaintSubTypeService $complaintSubType,
      ComplaintTypeService $complaintType)
   {
      $this->complaintSubType=$complaintSubType;
      $this->complaintType=$complaintType;
   }

   protected function stepProcess(BaseDTO $txDTO)
   {

      if(\count(\explode("*", $txDTO->customerJourney))==2){
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
         } catch (\Throwable $e) {
               if($e->getCode()==1){
                  $txDTO->errorType = "InvalidInput";
               }else{
                  $txDTO->errorType = 'SystemError';
               }
               $txDTO->error='At Retrieving complaint subtypes. '.$e->getMessage();
         }
      }
      return $txDTO;

   }

}