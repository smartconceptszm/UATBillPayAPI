<?php

namespace App\Http\DTOs;

use App\Http\Services\Enums\PaymentStatusEnum;
use App\Http\DTOs\PaymentDTO;

class BankDTO extends PaymentDTO
{

   public function fromSessionData(array $sessionParams): BaseDTO
   {
      
      foreach ($sessionParams as $key => $value) {
         if ((\property_exists($this, $key)) && ($key!='id')) {
               $this->$key = $value;
         }
      }
      $this->paymentStatus = PaymentStatusEnum::Paid;
      $this->channel = "BANK-RECEIPTING";
      $this->status = 'INITIATED';
      $this->session_id = '';
      $this->sms = [];
      return $this;
      
   }

   public function toPaymentData():array{
      return $this->getCommonPaymentData();
   }

   public function toProviderParams():object{
      return (object)[
            'transactionId'=>$this->transactionId,
            'paymentAmount'=>$this->paymentAmount,
            'receiptNumber'=>$this->receiptNumber,
            'transactionDate'=>$this->created_at,
            'wallet_id'=>$this->wallet_id,
            'urlPrefix'=>$this->urlPrefix,
            'message'=>"",
         ]; 
   }

}
