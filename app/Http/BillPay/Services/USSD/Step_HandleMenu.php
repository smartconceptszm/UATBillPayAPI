<?php

namespace App\Http\BillPay\Services\USSD;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineContract;

use App\Http\BillPay\Services\USSD\Menus\IUSSDMenu;
use Illuminate\Support\Facades\Cache;
use App\Http\BillPay\DTOs\BaseDTO;

class Step_HandleMenu extends EfectivoPipelineContract
{
    private $ussdMenu;
    public function __construct(IUSSDMenu $ussdMenu)
    {
        $this->ussdMenu = $ussdMenu;
    }
    
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
                $txDTO=$this->ussdMenu->handle($txDTO);   
            }
        } catch (\Throwable $e) {
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