<?php

namespace App\Http\BillPay\Services\USSD\Menus;

use App\Http\BillPay\Services\USSD\FaultsComplaints\ClientCallers\ComplaintClientBinderService;
use App\Http\BillPay\Services\USSD\Menus\IUSSDMenu;
use App\Http\BillPay\DTOs\BaseDTO;
use Illuminate\Pipeline\Pipeline;

class FaultsComplaints implements IUSSDMenu
{

   private $complaintClientBinderService;
   public function __construct(ComplaintClientBinderService $complaintClientBinderService)
   {
      $this->complaintClientBinderService = $complaintClientBinderService;
   }

   public function handle(BaseDTO $txDTO):BaseDTO
   {
        
      if($txDTO->error=='' ){
         try {
            //Bind the Complaint Client Creator
               $this->complaintClientBinderService->bind('Complaint_'.$txDTO->urlPrefix);
            //
            $txDTO->stepProcessed=false;
            $txDTO = app(Pipeline::class)
            ->send($txDTO)
            ->through(
               [  \App\Http\BillPay\Services\USSD\FaultsComplaints\FaultsComplaints_SubStep_1::class,
                  \App\Http\BillPay\Services\USSD\FaultsComplaints\FaultsComplaints_SubStep_2::class,
                  \App\Http\BillPay\Services\USSD\FaultsComplaints\FaultsComplaints_SubStep_3::class,
                  \App\Http\BillPay\Services\USSD\FaultsComplaints\FaultsComplaints_SubStep_4::class,                    
                  \App\Http\BillPay\Services\USSD\FaultsComplaints\FaultsComplaints_SubStep_5::class,
                  \App\Http\BillPay\Services\USSD\FaultsComplaints\FaultsComplaints_SubStep_6::class
               ]
            )
            ->thenReturn();
            $txDTO->stepProcessed=false;
         } catch (\Throwable $e) {
            $txDTO->error='At handle faults and complaints menu. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;

   }
    
}