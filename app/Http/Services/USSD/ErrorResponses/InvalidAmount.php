<?php

namespace App\Http\Services\USSD\ErrorResponses;

use App\Http\Services\USSD\ErrorResponses\IErrorResponse;
use Illuminate\Support\Facades\Cache;
use App\Http\DTOs\BaseDTO;
use Illuminate\Support\Carbon;

class InvalidAmount implements IErrorResponse
{

    public function handle(BaseDTO $txDTO):BaseDTO
    {

        try {  
            $txDTO->response = $txDTO->error;
            // $txDTO->response=   "Invalid amount. Amount must be at least ZMW ".
            //                         Cache::get($txDTO->urlPrefix.$txDTO->customerAccount."_MinPaymentAmount").
            //                         "\n\n<<Enter 0 to go back>>\n";
            // $txDTO->error=$txDTO->response;
            // $cacheValue = \json_encode([
            //                     'must'=>true,
            //                     'steps'=>1,
            //                 ]);
            // Cache::put($txDTO->sessionId."handleBack",$cacheValue, 
            //             Carbon::now()->addMinutes(intval(\env('SESSION_CACHE'))));
            $txDTO->lastResponse = true;
        } catch (\Throwable $e) {
            $txDTO->error = 'At Generate invalid amount response. '.$e->getMessage();
        }
        return $txDTO;
        
    }
}