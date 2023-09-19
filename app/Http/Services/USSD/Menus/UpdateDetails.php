<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\USSD\UpdateDetails\ClientCallers\UpdateDetailsClientBinderService;
use App\Http\Services\USSD\Menus\IUSSDMenu;
use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\BaseDTO;
use Exception;

class UpdateDetails implements IUSSDMenu
{

   public function __construct(
      private UpdateDetailsClientBinderService $updatesClientBinderService)
   {}

   public function handle(BaseDTO $txDTO):BaseDTO
   {
      
      if ($txDTO->error == '') {
         try {
            //Bind the Update Application Creator Client 
               $this->updatesClientBinderService->bind('Updates_'.$txDTO->urlPrefix);
            //
            $txDTO->stepProcessed=false;
            $txDTO = app(Pipeline::class)
            ->send($txDTO)
            ->through(
               [
                  \App\Http\Services\USSD\UpdateDetails\UpdateDetails_SubStep_1::class,
                  \App\Http\Services\USSD\UpdateDetails\UpdateDetails_SubStep_2::class,
                  \App\Http\Services\USSD\UpdateDetails\UpdateDetails_SubStep_3::class,
                  \App\Http\Services\USSD\UpdateDetails\UpdateDetails_SubStep_4::class,                    
                  \App\Http\Services\USSD\UpdateDetails\UpdateDetails_SubStep_5::class
               ]
            )
            ->thenReturn();
            $txDTO->stepProcessed=false;
         } catch (Exception $e) {
            $txDTO->error = 'At handle customer field update menu. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;
      
   }
    
}
