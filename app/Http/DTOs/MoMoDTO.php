<?php

namespace App\Http\DTOs;

use App\Http\DTOs\BaseDTO;

class MoMoDTO extends BaseDTO
{
   
   public $mnoTransactionId;
   public $surchargeAmount;
   public $accountNumber;
   public $paymentAmount;
   public $receiptAmount;
   public $transactionId;
   public $receiptNumber;
   public $paymentStatus;
   public $mobileNumber;
   public $meterNumber;
   public $tokenNumber;
   public $accountType;
   public $session_id;
   public $created_at;
   public $updated_at;
   public $client_id;
   public $reference;
   public $district;
   public $receipt;
   public $menu_id;
   public $user_id;
   public $mno_id;
   public $channel;
   public $status = 'INITIATED';
   public $error = '';

   public $customerJourney;   
   public $clientSurcharge;
   public $billingClient;
   public $mnoResponse;
   public $testMSISDN;
   public $sessionId;
   public $urlPrefix;
   public $shortCode;
   public $customer;
   public $mnoName;
   public $sms;

   public function fromUssdData(array $ussdParams): BaseDTO
   {
      foreach ($ussdParams as $key => $value) {
         if ((\property_exists($this, $key)) && ($key!='id')) {
               $this->$key = $value;
         }
      }
      $this->session_id = $ussdParams['id'];
      $this->status = 'INITIATED';
      $this->channel = "USSD";
      $this->sms = [];
      return $this;
   }

   public function toPaymentData():array{
      return [
            'mnoTransactionId'=>$this->mnoTransactionId,
            'surchargeAmount'=>$this->surchargeAmount,
            'accountNumber'=>$this->accountNumber,
            'paymentAmount'=>$this->paymentAmount,
            'transactionId'=>$this->transactionId,
            'receiptAmount'=>$this->receiptAmount,
            'paymentStatus'=>$this->paymentStatus,
            'receiptNumber'=>$this->receiptNumber,
            'mobileNumber'=>$this->mobileNumber,
            'meterNumber'=>$this->meterNumber,
            'tokenNumber'=>$this->tokenNumber,
            'session_id'=>$this->session_id,
            'client_id'=>$this->client_id,         
            'reference'=>$this->reference,
            'district'=>$this->district,
            'menu_id'=>$this->menu_id,
            'channel'=>$this->channel,
            'receipt'=>$this->receipt, 
            'user_id'=>$this->user_id,
            'status'=>$this->status,
            'mno_id'=>$this->mno_id,
            'error'=>$this->error,
            'id'=>$this->id
         ]; 
   }

   public function toMoMoParams():object{
      return (object)[
            'transactionId'=>$this->transactionId,
            'accountNumber'=>$this->accountNumber,
            'paymentAmount'=>$this->paymentAmount,
            'mobileNumber'=>$this->mobileNumber,
            'urlPrefix'=>$this->urlPrefix 
         ]; 
   }

}
