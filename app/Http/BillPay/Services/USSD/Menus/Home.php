<?php

namespace App\Http\BillPay\Services\USSD\Menus;

use App\Http\BillPay\Services\USSD\Menus\IUSSDMenu;
use App\Http\BillPay\DTOs\BaseDTO;

class Home implements IUSSDMenu
{

    public function handle(BaseDTO $txDTO):BaseDTO
    {
        
        if($txDTO->error==''){
            try {
                $txDTO->response=\config('efectivo_clients.'.$txDTO->urlPrefix.'.Home');
            } catch (\Throwable $e) {
                $txDTO->error='At handle new session. '.$e->getMessage();
                $txDTO->errorType = 'SystemError';
            }  
        } 
        return $txDTO;

    }   

}