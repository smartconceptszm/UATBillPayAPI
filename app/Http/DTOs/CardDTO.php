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
      
      $cardNumber = 'VISA-****-****-'.substr($this->walletNumber,-4);
      return [
            'ppTransactionId'=>$this->ppTransactionId,
            'surchargeAmount'=>$this->surchargeAmount,
            'customerAccount'=>$this->customerAccount,
            'paymentAmount'=>$this->paymentAmount,
            'transactionId'=>$this->transactionId,
            'receiptAmount'=>$this->receiptAmount,
            'paymentStatus'=>$this->paymentStatus,
            'receiptNumber'=>$this->receiptNumber,
            'mobileNumber'=>$this->mobileNumber,
            'tokenNumber'=>$this->tokenNumber,
            'session_id'=>$this->session_id,
            'wallet_id'=>$this->wallet_id,         
            'reference'=>$this->reference,
            'district'=>$this->district,
            'walletNumber'=>$cardNumber,
            'menu_id'=>$this->menu_id,
            'channel'=>$this->channel,
            'receipt'=>$this->receipt, 
            'user_id'=>$this->user_id,
            'status'=>$this->status,
            'error'=>$this->error,
            'id'=>$this->id
         ]; 
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
