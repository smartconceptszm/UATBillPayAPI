<?php

namespace App\Http\DTOs;


/**
 * @property $id
 * @property string $response
 * @property string $error
 */

class WebDTO extends BaseDTO
{
   
   public $clientSurcharge;
   public $billingClient;
   public $accountNumber;
   public $paymentAmount;
   public $mobileNumber;
   public $testMSISDN;
   public $created_at;
   public $updated_at;
   public $urlPrefix;
   public $sessionId;
   public $client_id;
   public $reference;
   public $isPayment;
   public $mnoName;
   public $handler;
   public $channel;
   public $menu_id;
   public $mno_id;
   public $status = 'INITIATED';
   public $error;

   public function toSessionData():array{
      return [
            'accountNumber'=>$this->accountNumber,
            'paymentAmount'=>$this->paymentAmount,
            'mobileNumber'=>$this->mobileNumber,
            'sessionId'=>$this->sessionId,
            'client_id'=>$this->client_id,
            'menu_id'=>$this->menu_id,
            'status'=>$this->status,
            'mno_id'=>$this->mno_id,
            'error'=>$this->error,
            'id'=>$this->id
         ];
   }
    
}
