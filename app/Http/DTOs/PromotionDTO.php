<?php

namespace App\Http\DTOs;

use App\Http\DTOs\BaseDTO;

class PromotionDTO extends BaseDTO
{

    public $customerAccount;
    public $paymentAmount;
    public $receiptNumber;
    public $consumerType;
    public $promotion_id;
    public $rewardAmount;
    public $mobileNumber;
    public $paymentType;
    public $rewardRate;
    public $wallet_id;
    public $client_id;
    public $entryDate;
    public $message;
    public $status;

    public $enterPromo = true;

    public $raffleEntryMessage;
    public $totalMonthAmount;

    public $promotionRaffleEntryMessage;
    public $promotionRaffleEntryAmount;
    public $promotionEntryMessage;
    public $promotionConsumerType;
    public $promotionEntryAmount;
    public $promotionRateValue;
    public $promotionOnDebt;
    public $promotionName;
    public $promotionType;

    public $payment_id;
    public $created_at;
    public $updated_at;
    public $urlPrefix;
    public $menu_id;
    public $mno_id;
    public $error = '';

    public function toPromotionEntryData(): array
    {
        
        return [
            'customerAccount' => $this->customerAccount,
            'paymentAmount' => $this->paymentAmount,
            'receiptNumber' => $this->receiptNumber,
            'promotion_id' => $this->promotion_id,
            'rewardAmount' => $this->rewardAmount,
            'consumerType' => $this->consumerType,
            'mobileNumber' => $this->mobileNumber,
            'paymentType' => $this->paymentType,
            'payment_id' => $this->payment_id,
            'rewardRate' => $this->rewardRate,
            'entryDate' => $this->entryDate,
            'message' => $this->message
        ];

    }

    public function toRaffleEntryData(): array
    {

        return [
            'customerAccount' => $this->customerAccount,
            'receiptNumber' => $this->receiptNumber,
            'paymentAmount' => $this->paymentAmount,
            'message' => $this->raffleEntryMessage,
            'consumerType' => $this->consumerType,
            'mobileNumber' => $this->mobileNumber,
            'promotion_id' => $this->promotion_id,
            'promotion_entry_id' => $this->id,
            'entryDate' => $this->entryDate,
        ];

    }

    public function toSMSData(): array
    {
        
        return [
            'customerAccount' => $this->customerAccount,
            'mobileNumber' => $this->mobileNumber,
            'wallet_id' => $this->wallet_id,
            'client_id' => $this->client_id,
            'message' => $this->message,
            'mno_id' => $this->mno_id,
            'status' => 'INITIATED',
            'type' => "PROMOTION",
            'user_id' => null,
            'error' => "",
        ];

    }

}
