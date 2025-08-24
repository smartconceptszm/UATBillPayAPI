<?php

namespace App\Http\DTOs;

use App\Http\DTOs\BaseDTO;

class CompositeReceiptDTO extends BaseDTO
{
    public $id;
    public $ppTransactionId;
    public $customerAccount;
    public $receiptAmount;
    public $receiptNumber;
    public $walletHandler;
    public $tokenNumber;
    public $payment_id;
    public $created_at;
    public $updated_at;
    public $client_id;
    public $menu_id;
    public $balance;
    public $status = 'PAID | NOT RECEIPTED';
    public $error = '';

    public function toReceiptData(): array
    {

        return [
            'customerAccount' => $this->customerAccount,
            'receiptAmount' => $this->receiptAmount,
            'receiptNumber' => $this->receiptNumber,
            'tokenNumber' => $this->tokenNumber,
            'payment_id' => $this->payment_id,
            'client_id' => $this->client_id,
            'status' => $this->status,
            'error' => $this->error,
        ];

    }

}
