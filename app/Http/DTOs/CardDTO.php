<?php

namespace App\Http\DTOs;

use App\Http\DTOs\PaymentDTO;

class CardDTO extends PaymentDTO
{

   public $cardHolderName;
   public $cardExpiry;
   public $cardCVV;

   public function fromSessionData(array $sessionParams): BaseDTO
   {
      
      foreach ($sessionParams as $key => $value) {
         if ((\property_exists($this, $key)) && ($key!='id')) {
               $this->$key = $value;
         }
      }
      $this->session_id = $sessionParams['id'];
      $this->status = 'INITIATED';
      $this->channel = "WEBSITE";
      $this->sms = [];
      return $this;
   }

   public function toProviderParams():object{
      return (object)[
            'cardHolderName'=>$this->cardHolderName,
            'transactionId'=>$this->transactionId,
            'accountNumber'=>$this->accountNumber,
            'paymentAmount'=>$this->paymentAmount,
            'mobileNumber'=>$this->mobileNumber,
            'walletNumber'=>$this->walletNumber,
            'wallet_id'=>$this->wallet_id,
            'walletCVV'=>$this->walletCVV
         ]; 
   }

}
