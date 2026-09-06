<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\USSD\Menus\IUSSDMenu;
use App\Http\Services\Enums\USSDStatusEnum;
use Illuminate\Support\Carbon;
use App\Jobs\SendSMSesJob;
use App\Http\DTOs\BaseDTO;


class RateUs implements IUSSDMenu
{

   public function handle(BaseDTO $txDTO):BaseDTO
   {

      try {
         if($txDTO->error==''){
            $txDTO->response = "Please check your SMS and click on the link to rate us";
            $this->sendSMSNotification($txDTO);
            $txDTO->lastResponse = true;
            $txDTO->status = USSDStatusEnum::Completed->value;
         }
      } catch (\Throwable $e) {
         $txDTO->error = 'At handle rate us menu. '.$e->getMessage();
         $txDTO->errorType = USSDStatusEnum::SystemError->value;
      }
      return $txDTO;

   }

   private function sendSMSNotification(BaseDTO $txDTO): void

   {

       // Add invisible zero-width character between https and ://
      $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
      $key = 'RATE_US_URL_' . \strtoupper($txDTO->urlPrefix);
      $shortUrl = $billpaySettings[$key];

      $arrSMSes = [
               [
                  'mobileNumber' => $txDTO->mobileNumber,
                  'urlPrefix' => $txDTO->urlPrefix,
                  'client_id' => $txDTO->client_id,
                  'message' => "{$shortUrl}",
                  'type' => 'NOTIFICATION',
               ]


         ];

      SendSMSesJob::dispatch($arrSMSes)
                     ->delay(Carbon::now()->addSeconds(3))
                     ->onQueue('low');
   }

}