<?php

namespace App\Http\Services\Gateway\ConfirmPaymentSteps;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Payments\ShortcutCustomerService;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Http\DTOs\BaseDTO;

class Step_AddShortcutMessageToReceipt extends EfectivoPipelineContract
{
    public function __construct(
        private ShortcutCustomerService $shortcutCustomerService,
        private ClientMenuService $clientMenuService
    ) {}

    protected function stepProcess(BaseDTO $paymentDTO)
    {
        try {

            if($paymentDTO->paymentStatus == PaymentStatusEnum::Receipted->value ){

                $customer = $this->shortcutCustomerService->findOneBy([
                                            'customerAccount'=>$paymentDTO->customerAccount,
                                            'mobileNumber' => $paymentDTO->mobileNumber
                                        ]);

                if($customer && $paymentDTO->channel== 'USSD'){
                    $theMenu = $this->clientMenuService->findById($paymentDTO->menu_id);
                    if($theMenu->shortcut){
                        $shortcutMessage = "Dial *".$theMenu->shortcut." to pay.". "\n";
                        if((strlen($paymentDTO->receipt) + strlen($shortcutMessage))<160){
                            $paymentDTO->receipt .= $shortcutMessage;
                        }
                    }
                }
            }

        } catch (\Throwable $e) {
            $paymentDTO->error = 'At add shortcut message to receipt step. ' . $e->getMessage();
        }

        return $paymentDTO;
    }

}
