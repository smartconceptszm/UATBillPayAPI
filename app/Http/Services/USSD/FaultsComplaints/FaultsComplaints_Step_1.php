<?php

namespace App\Http\Services\USSD\FaultsComplaints;

use App\Http\Services\MenuConfigs\ComplaintTypeService;
use App\Http\DTOs\BaseDTO;
use Exception;

class FaultsComplaints_Step_1
{

   public function __construct(
      private ComplaintTypeService $complaintType)
   {}

   public function run(BaseDTO $txDTO)
   {

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
      return $txDTO;

   }

}