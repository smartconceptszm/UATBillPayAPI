<?php

namespace App\Http\DTOs;

use App\Http\DTOs\BaseDTO;

abstract class PaymentDTO extends BaseDTO
{

   public $payments_provider_id;
   public $ppTransactionId;
   public $surchargeAmount;
   public $accountNumber;
   public $paymentAmount;
   public $receiptAmount;
   public $transactionId;
   public $receiptNumber;
   public $paymentStatus;
   public $walletHandler;
   public $mobileNumber;
   public $walletNumber;
   public $meterNumber;
   public $tokenNumber;
   public $accountType;
   public $session_id;
   public $created_at;
   public $updated_at;
   public $wallet_id;
   public $client_id;
   public $reference;
   public $district;
   public $receipt;
   public $menu_id;
   public $user_id;
   public $channel;
   public $status = 'INITIATED';
   public $error = '';

   public $customerJourney;   
   public $clientSurcharge;
   public $enquiryHandler;
   public $testMSISDN;
   public $sessionId;
   public $urlPrefix;
   public $shortCode;
   public $customer;
   public $mno_id;
   public $sms;

   protected abstract function fromSessionData(array $ussdParams): BaseDTO;

   public function toPaymentData():array{
      return [
            'ppTransactionId'=>$this->ppTransactionId,
            'surchargeAmount'=>$this->surchargeAmount,
            'accountNumber'=>$this->accountNumber,
            'paymentAmount'=>$this->paymentAmount,
            'transactionId'=>$this->transactionId,
            'receiptAmount'=>$this->receiptAmount,
            'paymentStatus'=>$this->paymentStatus,
            'receiptNumber'=>$this->receiptNumber,
            'mobileNumber'=>$this->mobileNumber,
            'walletNumber'=>$this->walletNumber,
            'meterNumber'=>$this->meterNumber,
            'tokenNumber'=>$this->tokenNumber,
            'session_id'=>$this->session_id,
            'wallet_id'=>$this->wallet_id,
            'client_id'=>$this->client_id,         
            'reference'=>$this->reference,
            'district'=>$this->district,
            'menu_id'=>$this->menu_id,
            'channel'=>$this->channel,
            'receipt'=>$this->receipt, 
            'user_id'=>$this->user_id,
            'status'=>$this->status,
            'error'=>$this->error,
            'id'=>$this->id
         ]; 
   }

   protected abstract function toProviderParams():object;

}
