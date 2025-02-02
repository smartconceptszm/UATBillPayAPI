<?php

namespace App\Http\Services\USSD\FaultsComplaints;

use App\Http\Services\MenuConfigs\ComplaintSubTypeService;
use App\Http\Services\MenuConfigs\ComplaintTypeService;
use App\Http\Services\Enums\USSDStatusEnum;
use App\Http\DTOs\BaseDTO;
use Exception;

class FaultsComplaints_Step_2
{

   public function __construct(
      private ComplaintSubTypeService $complaintSubType,
      private ComplaintTypeService $complaintType)
   {}

   public function run(BaseDTO $txDTO)
   {
 
      try {

         $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
         $theComplaintType = $this->complaintType->findOneBy([
                                          'order'=>$txDTO->subscriberInput,
                                          'client_id'=>$txDTO->client_id,
                                    ]);
         if($theComplaintType){
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
         if($e->getCode() == 1){
            $txDTO->errorType = USSDStatusEnum::InvalidInput->value;
         }else{
            $txDTO->errorType = USSDStatusEnum::SystemError->value;
         }
         $txDTO->error='At complaints step 2. '.$e->getMessage();
      }
      return $txDTO;

   }

}