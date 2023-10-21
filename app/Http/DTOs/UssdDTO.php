<?php

namespace App\Http\DTOs;


/**
 * @property $id
 * @property string $response
 * @property string $error
 */

class UssdDTO extends BaseDTO
{
   
   public $clientSurcharge;
   public $subscriberInput;
   public $fireMoMoRequest;
   public $customerJourney;
   public $billingClient;
   public $accountNumber;
   public $paymentAmount;
   public $stepProcessed;
   public $lastResponse;
   public $isNewRequest;
   public $mobileNumber;
   public $testMSISDN;
   public $menuPrompt;
   public $created_at;
   public $clientCode;
   public $urlPrefix;
   public $sessionId;
   public $client_id;
   public $reference;
   public $errorType;
   public $isPayment;
   public $customer;
   public $district;
   public $response;
   public $mnoName;
   public $handler;
   public $menu_id;
   public $mno_id;
   public $status = 'INITIATED';
   public $clean;
   public $error;

   public function toSessionData():array{
      return [
            'customerJourney'=>$this->customerJourney, 
            'accountNumber'=>$this->accountNumber,
            'paymentAmount'=>$this->paymentAmount,
            'mobileNumber'=>$this->mobileNumber,
            'sessionId'=>$this->sessionId,
            'client_id'=>$this->client_id,
            'district'=>$this->district,
            'response'=>$this->response,
            'menu_id'=>$this->menu_id,
            'status'=>$this->status,
            'mno_id'=>$this->mno_id,
            'error'=>$this->error,
            'id'=>$this->id
         ];
   }
    
}
