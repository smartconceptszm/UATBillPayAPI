<?php

namespace App\Http\BillPay\Services\USSD\Menus;

use App\Http\BillPay\Services\USSD\Menus\MenuService_PaymentSteps;
use App\Http\BillPay\Services\USSD\Menus\IUSSDMenu;
use App\Http\BillPay\DTOs\BaseDTO;

class PayBill implements IUSSDMenu
{
    
    private $paymentSteps;
    public function __construct(MenuService_PaymentSteps $paymentSteps)
    {
        $this->paymentSteps=$paymentSteps;
    }

    public function handle(BaseDTO $txDTO):BaseDTO
    {
        
        if ($txDTO->error == '') {
            try {
                $txDTO=$this->paymentSteps->handle($txDTO);
            } catch (\Throwable $e) {
                $txDTO->error='At handle paybill menu. '.$e->getMessage();
                $txDTO->errorType = 'SystemError';
            } 
        }
        return $txDTO;
    }
    
}
