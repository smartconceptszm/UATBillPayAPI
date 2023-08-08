<?php

namespace App\Http\BillPay\Services\USSD\Menus;

use App\Http\BillPay\Services\USSD\Survey\ClientCallers\SurveyClientBinderService;
use App\Http\BillPay\Services\USSD\Menus\IUSSDMenu;
use App\Http\BillPay\DTOs\BaseDTO;
use Illuminate\Pipeline\Pipeline;

class Survey implements IUSSDMenu
{

   private $surveyClientBinderService;
   public function __construct(SurveyClientBinderService $surveyClientBinderService)
   {
      $this->surveyClientBinderService = $surveyClientBinderService;
   }

   public function handle(BaseDTO $txDTO):BaseDTO
   {
      
      if ($txDTO->error == '') {
         try {
            //Bind the Survey Entry Creator Client
               $this->surveyClientBinderService->bind('Survey_'.$txDTO->urlPrefix);
            //
            $txDTO->stepProcessed=false;
            $txDTO = app(Pipeline::class)
            ->send($txDTO)
            ->through(
               [
                  \App\Http\BillPay\Services\USSD\Survey\Survey_SubStep_1::class,
                  \App\Http\BillPay\Services\USSD\Survey\Survey_SubStep_2::class,
                  \App\Http\BillPay\Services\USSD\Survey\Survey_SubStep_3::class,
                  \App\Http\BillPay\Services\USSD\Survey\Survey_SubStep_5::class
               ]
            )
            ->thenReturn();
            $txDTO->stepProcessed=false;
         } catch (\Throwable $e) {
            $txDTO->error='At handle survey menu. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;
      
   }
    
}
