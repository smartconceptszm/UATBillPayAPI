<?php

namespace App\Http\BillPay\Services\USSD\Menus;

use App\Http\BillPay\Services\USSD\UpdateDetails\ClientCallers\UpdateDetailsClientBinderService;
use App\Http\BillPay\Services\USSD\Menus\IUSSDMenu;
use App\Http\BillPay\DTOs\BaseDTO;
use Illuminate\Pipeline\Pipeline;

class UpdateDetails implements IUSSDMenu
{

   private $updatesClientBinderService;
   public function __construct(UpdateDetailsClientBinderService $updatesClientBinderService)
   {
      $this->updatesClientBinderService = $updatesClientBinderService;
   }

   public function handle(BaseDTO $txDTO):BaseDTO
   {
      
      if ($txDTO->error == '') {
         try {
            //Bind the Complaint Client Creator
               $this->updatesClientBinderService->bind('Updates_'.$txDTO->urlPrefix);
            //
            $txDTO->stepProcessed=false;
            $txDTO = app(Pipeline::class)
            ->send($txDTO)
            ->through(
               [
                  \App\Http\BillPay\Services\USSD\UpdateDetails\UpdateDetails_SubStep_1::class,
                  \App\Http\BillPay\Services\USSD\UpdateDetails\UpdateDetails_SubStep_2::class,
                  \App\Http\BillPay\Services\USSD\UpdateDetails\UpdateDetails_SubStep_3::class,
                  \App\Http\BillPay\Services\USSD\UpdateDetails\UpdateDetails_SubStep_4::class,                    
                  \App\Http\BillPay\Services\USSD\UpdateDetails\UpdateDetails_SubStep_5::class
               ]
            )
            ->thenReturn();
            $txDTO->stepProcessed=false;
         } catch (\Throwable $e) {
            $txDTO->error='At handle customer field update menu. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;
      
   }
    
}
