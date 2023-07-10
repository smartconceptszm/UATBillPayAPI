<?php

namespace App\Http\BillPay\Services\USSD\Menus;

use App\Http\BillPay\Services\USSD\Menus\IUSSDMenu;
use App\Http\BillPay\DTOs\BaseDTO;

class CleanupSession implements IUSSDMenu
{

    public function handle(BaseDTO $txDTO):BaseDTO
    {
        
        if($txDTO->error==''){
            $txDTO->response = '';
            $txDTO->status = 'COMPLETED'; 
            $txDTO->lastResponse= true;
        } 
        return $txDTO;

    }   

}