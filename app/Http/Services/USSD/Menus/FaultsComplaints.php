<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\USSD\FaultsComplaints\ClientCallers\ComplaintClientBinderService;
use App\Http\Services\USSD\Menus\IUSSDMenu;
use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\BaseDTO;
use Exception;

class FaultsComplaints implements IUSSDMenu
{

   public function __construct(
      private ComplaintClientBinderService $complaintClientBinderService)
   {}

   public function handle(BaseDTO $txDTO):BaseDTO
   {
        
      if($txDTO->error=='' ){
         try {
            //Bind the Complaint Creator Client 
               $this->complaintClientBinderService->bind('Complaint_'.$txDTO->urlPrefix);
            //
            $txDTO->stepProcessed=false;
            $txDTO = app(Pipeline::class)
            ->send($txDTO)
            ->through(
               [  \App\Http\Services\USSD\FaultsComplaints\FaultsComplaints_SubStep_1::class,
                  \App\Http\Services\USSD\FaultsComplaints\FaultsComplaints_SubStep_2::class,
                  \App\Http\Services\USSD\FaultsComplaints\FaultsComplaints_SubStep_3::class,
                  \App\Http\Services\USSD\FaultsComplaints\FaultsComplaints_SubStep_4::class,                    
                  \App\Http\Services\USSD\FaultsComplaints\FaultsComplaints_SubStep_5::class,
                  \App\Http\Services\USSD\FaultsComplaints\FaultsComplaints_SubStep_6::class
               ]
            )
            ->thenReturn();
            $txDTO->stepProcessed=false;
         } catch (Exception $e) {
            $txDTO->error = 'At handle faults and complaints menu. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;

   }
    
}