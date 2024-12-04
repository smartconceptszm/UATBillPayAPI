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

   public function toPaymentData():array{
      $paymentData = $this->getCommonPaymentData();
      $paymentData['walletNumber'] = 'VISA-****-****-' . substr($this->walletNumber, -4);
      return $paymentData;
   }

   public function toProviderParams():object{
      return (object)[
            'cardHolderName'=>$this->cardHolderName,
            'creditCardNumber'=>$this->walletNumber,
            'transactionId'=>$this->transactionId,
            'paymentAmount'=>$this->paymentAmount,
            'transactionDate'=>$this->created_at,
            'cardExpiry'=>$this->cardExpiry,
            'wallet_id'=>$this->wallet_id,
            'cardCVV'=>$this->cardCVV
         ]; 
   }

}
