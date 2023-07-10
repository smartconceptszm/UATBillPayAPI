<?php

namespace App\Http\BillPay\DTOs;

use App\Http\BillPay\DTOs\BaseDTO;

class SMSTxDTO extends BaseDTO
{
   
   public $transaction_id;
   public $mobileNumber;
   public $smsPayMode;
   public $client_id;
   public $urlPrefix;
   public $shortName;
   public $smsCharge;
   public $balance;
   public $user_id;
   public $message;
   public $status = 'INITIATED';
   public $mno_id;
   public $type = 'SINGLE';
   public $error;

   public $mnoResponse;

   public $validationRules=[
      'mobileNumber' => 'required|string|size:12',
      'client_id' => 'required|string',
      'message' => 'required|string'
   ];

   public function fromMoMoDTO(BaseDTO $txDTO): BaseDTO
   {
      $this->transaction_id = $txDTO->transactionId;
      $this->mobileNumber = $txDTO->mobileNumber;
      $this->urlPrefix = $txDTO->urlPrefix;
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
               'transaction_id'=>$this->transaction_id,
               'mobileNumber'=>$this->mobileNumber,
               'client_id'=>$this->client_id,
               'message'=>$this->message,
               'mno_id'=>$this->mno_id,
               'user_id'=>$this->user_id,
               'amount'=>$this->smsCharge,
               'status'=>$this->status,
               'error'=>$this->error,
               'type'=>$this->type
            ];
   }

   public function toSMSClientData():array{
      return [
            'transactionId'=>$this->transaction_id,
            'clientShortName'=>$this->shortName,
            'mobileNumber'=>$this->mobileNumber,
            'urlPrefix'=>$this->urlPrefix,
            'message'=>$this->message
         ];
   }

}
