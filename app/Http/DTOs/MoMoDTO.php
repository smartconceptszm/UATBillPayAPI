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

   public function toPaymentData():array{
      
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
            'walletNumber'=>$this->walletNumber,
            'tokenNumber'=>$this->tokenNumber,
            'session_id'=>$this->session_id,
            'wallet_id'=>$this->wallet_id,      
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

   public function toProviderParams():object{
      return (object)[
            'customerAccount'=>$this->customerAccount,
            'transactionId'=>$this->transactionId,
            'paymentAmount'=>$this->paymentAmount,
            'mobileNumber'=>$this->mobileNumber,
            'walletNumber'=>$this->walletNumber,
            'wallet_id'=>$this->wallet_id
         ]; 
   }

}
