<?php

namespace App\Http\DTOs;

use App\Http\DTOs\PaymentDTO;

class MoMoDTO extends PaymentDTO
{

   public function fromSessionData(array $ussdParams): BaseDTO
   {
      foreach ($ussdParams as $key => $value) {
         if ((\property_exists($this, $key)) && ($key!='id')) {
               $this->$key = $value;
         }
      }
      $this->walletNumber = $ussdParams['mobileNumber'];
      $this->session_id = $ussdParams['id'];
      $this->status = 'INITIATED';
      $this->channel = "USSD";
      $this->sms = [];
      return $this;
   }

   public function toProviderParams():object{
      return (object)[
            'transactionId'=>$this->transactionId,
            'accountNumber'=>$this->accountNumber,
            'paymentAmount'=>$this->paymentAmount,
            'mobileNumber'=>$this->mobileNumber,
            'walletNumber'=>$this->walletNumber,
            'wallet_id'=>$this->wallet_id
         ]; 
   }

}
