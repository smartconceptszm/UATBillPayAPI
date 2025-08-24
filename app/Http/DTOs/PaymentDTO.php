<?php

namespace App\Http\DTOs;

use App\Http\DTOs\BaseDTO;

abstract class PaymentDTO extends BaseDTO
{

    public $compositeAccount = FALSE;
    public $payments_provider_id;
    public $revenueCollector;
    public $ppTransactionId;
    public $surchargeAmount;
    public $customerAccount;
    public $paymentAmount;
    public $receiptAmount;
    public $transactionId;
    public $receiptNumber;
    public $paymentStatus;
    public $walletHandler;
    public $consumerTier;
    public $revenuePoint;
    public $consumerType;
    public $mobileNumber;
    public $walletNumber;
    public $tokenNumber;
    public $session_id;
    public $created_at;
    public $updated_at;
    public $wallet_id;
    public $client_id;
    public $reference;
    public $composite = 'ORDINARY';
    public $receipt;
    public $menu_id;
    public $user_id;
    public $channel;
    public $status = 'INITIATED';
    public $error = '';
    public $customerJourney;   
    public $clientSurcharge;
    public $smsHandler;
    public $testMSISDN;
    public $sessionId;
    public $urlPrefix;
    public $shortCode;
    public $customer;
    public $mno_id;
    public $sms;

    protected function getCommonPaymentData(): array
    {
        return [
            'revenueCollector' => $this->revenueCollector,
            'ppTransactionId' => $this->ppTransactionId,
            'surchargeAmount' => $this->surchargeAmount,
            'customerAccount' => $this->customerAccount,
            'customerJourney' => $this->customerJourney,
            'paymentAmount' => $this->paymentAmount,
            'transactionId' => $this->transactionId,
            'receiptAmount' => $this->receiptAmount,
            'paymentStatus' => $this->paymentStatus,
            'receiptNumber' => $this->receiptNumber,
            'consumerTier' => $this->consumerTier,
            'consumerType' => $this->consumerType,
            'revenuePoint' => $this->revenuePoint,
            'mobileNumber' => $this->mobileNumber,
            'walletNumber' => $this->walletNumber,
            'tokenNumber' => $this->tokenNumber,
            'session_id' => $this->session_id,
            'wallet_id' => $this->wallet_id,
            'reference' => $this->reference,
            'menu_id' => $this->menu_id,
            'channel' => $this->channel,
            'receipt' => $this->receipt,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'error' => $this->error,
        ];
    }

    protected abstract function fromSessionData(array $ussdParams): BaseDTO;

    protected abstract function toPaymentData(): array;

    protected abstract function toProviderParams():object;

}
