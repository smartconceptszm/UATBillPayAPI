<?php

namespace App\Http\BillPay\Services\USSD;

use App\Http\BillPay\Services\USSD\ErrorResponses\ErrorResponseBinderService;
use App\Http\BillPay\Services\Contracts\EfectivoPipelineContract;
use App\Http\BillPay\Services\USSD\SessionService;
use App\Http\BillPay\DTOs\BaseDTO;

class Step_SaveSession extends EfectivoPipelineContract
{

    private $errorResponseBinder;
    private $sessionService;
    public function __construct(SessionService $sessionService,
        ErrorResponseBinderService $errorResponseBinder)
    {
        $this->errorResponseBinder=$errorResponseBinder;
        $this->sessionService=$sessionService;
    }

    protected function stepProcess(BaseDTO $txDTO)
    {
        
        try {
            if( $txDTO->customerJourney){
                $txDTO->customerJourney=$txDTO->customerJourney."*".$txDTO->subscriberInput;
            }else{
                $txDTO->customerJourney=$txDTO->subscriberInput;
            }
            $txDTO->error = $txDTO->error? \substr($txDTO->error,0,255):'';
            if($txDTO->id){
                $this->sessionService->update($txDTO->toSessionData(),$txDTO->id);
            }
        } catch (\Throwable $e) {
            $txDTO->error='At Save session. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';        
        }
        //Bind error response service to interface
        $txDTO->errorType = $txDTO->error? $txDTO->errorType:"NoError";   
        $this->errorResponseBinder->bind($txDTO->errorType);

        return $txDTO;
        
    }
    
}