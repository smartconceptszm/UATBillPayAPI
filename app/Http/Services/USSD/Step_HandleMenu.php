<?php

namespace App\Http\Services\USSD;

use App\Http\Services\Contracts\EfectivoPipelineContract;

use App\Http\Services\USSD\Menus\IUSSDMenu;
use Illuminate\Support\Facades\Cache;
use App\Http\DTOs\BaseDTO;
use Exception;

class Step_HandleMenu extends EfectivoPipelineContract
{

    public function __construct(
        private IUSSDMenu $ussdMenu)
    {}
    
    protected function stepProcess(BaseDTO $txDTO)
    {

        try {
            if($txDTO->error == ''){
                $responseNext= Cache::get($txDTO->sessionId."responseNext",'');
                if($responseNext && $txDTO->subscriberInput ==='00'){
                    Cache::forget($txDTO->sessionId."responseNext");
                    $txDTO = $this->resetCustomerJourney($txDTO);
                    $txDTO->response = $responseNext;
                    return $txDTO;
                }
                if($responseNext && $txDTO->subscriberInput === '0'){
                    Cache::forget($txDTO->sessionId."responseNext");
                    $txDTO = $this->resetCustomerJourney($txDTO);
                }
                $txDTO = $this->ussdMenu->handle($txDTO);   
            }
        } catch (Exception $e) {
            $txDTO->error = 'At handle menu option. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
        }
        return $txDTO;

    }

    private function resetCustomerJourney(BaseDTO $txDTO): BaseDTO
    {

        $arrCustomerJourney = \explode("*", $txDTO->customerJourney);
        $txDTO->subscriberInput=$arrCustomerJourney[\count($arrCustomerJourney)-1];
        \array_pop($arrCustomerJourney);
        if(\count($arrCustomerJourney) > 0){
            if(\count($arrCustomerJourney) == 1){
                $txDTO->customerJourney = $arrCustomerJourney[0];
            }else{
                $txDTO->customerJourney = \implode("*", $arrCustomerJourney);
            }
        }else{
            $txDTO->customerJourney = '';
            $txDTO->menu = 'Home';
        }
        return $txDTO;
        
    }

}