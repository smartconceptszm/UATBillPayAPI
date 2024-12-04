<?php

namespace App\Http\DTOs;

use App\Http\DTOs\BaseDTO;

class SMSTxDTO extends BaseDTO
{
   
   public $customerAccount;
   public $sms_provider_id;
   public $transaction_id;
   public $mobileNumber;
   public $smsPayMode;
   public $channel_id;
   public $wallet_id;
   public $client_id;
   public $urlPrefix;
   public $smsCharge;
   public $handler;
   public $balance;
   public $user_id;
   public $message;
   public $mno_id;
   public $status = 'INITIATED';
   public $type = 'SINGLE';
   public $error;

   public $validationRules = [
                              'mobileNumber' => 'required|string|size:12',
                              'client_id' => 'required|string',
                              'message' => 'required|string'
                           ];

   public function fromPaymentDTO(BaseDTO $txDTO): BaseDTO
   {
      $this->customerAccount = $txDTO->customerAccount;
      $this->transaction_id = $txDTO->transactionId;
      $this->mobileNumber = $txDTO->mobileNumber;
      $this->wallet_id = $txDTO->wallet_id;
      $this->client_id = $txDTO->client_id;
      $this->message = $txDTO->receipt;
      $this->mno_id = $txDTO->mno_id;
      $this->status = 'INITIATED';
      $this->type = 'RECEIPT';
      $this->user_id = null;
      $this->error = '';
      return $this;
   }

   public function toSMSMessageData():array{
      return [
            'customerAccount'=>$this->customerAccount,
            'transaction_id'=>$this->transaction_id,
            'mobileNumber'=>$this->mobileNumber,
            'client_id'=>$this->client_id,
            'amount'=>$this->smsCharge,
            'message'=>$this->message,
            'user_id'=>$this->user_id,
            'mno_id'=>$this->mno_id,
            'status'=>$this->status,
            'error'=>$this->error,
            'type'=>$this->type
         ];
   }

   public function toSMSClientData():array{
      return [
            'sms_provider_id'=>$this->sms_provider_id,
            'transactionId'=>$this->transaction_id,
            'mobileNumber'=>$this->mobileNumber,
            'channel_id'=>$this->channel_id,
            'wallet_id'=>$this->wallet_id,
            'mno_id'=>$this->mno_id,
            'message'=>$this->message
         ];
   }

}
