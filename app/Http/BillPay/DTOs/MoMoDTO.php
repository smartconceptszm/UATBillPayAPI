<?php

namespace App\Http\BillPay\DTOs;

use App\Http\BillPay\DTOs\BaseDTO;

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
   public $session_id;
   public $client_id;
   public $district;
   public $receipt;
   public $user_id;
   public $mno_id;
   public $channel;
   public $status = 'INITIATED';
   public $error = '';

   public $customerJourney;
   public $stepProcessed;
   public $mnoResponse;
   public $clientCode;
   public $sessionId;
   public $urlPrefix;
   public $customer;
   public $mnoName;
   public $menu;
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
            'id'=>$this->id,'transactionId'=>$this->transactionId,
            'surchargeAmount'=>$this->surchargeAmount,'error'=>$this->error,
            'receiptAmount'=>$this->receiptAmount,'user_id'=>$this->user_id,
            'accountNumber'=>$this->accountNumber,'receipt'=>$this->receipt,
            'paymentAmount'=>$this->paymentAmount,'status'=>$this->status,
            'mobileNumber'=>$this->mobileNumber,'channel'=>$this->channel,
            'district'=>$this->district,'client_id'=>$this->client_id,
            'session_id'=>$this->session_id,'mno_id'=>$this->mno_id,
            'mnoTransactionId'=>$this->mnoTransactionId,
            'receiptNumber'=>$this->receiptNumber,
            'paymentStatus'=>$this->paymentStatus,   
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
