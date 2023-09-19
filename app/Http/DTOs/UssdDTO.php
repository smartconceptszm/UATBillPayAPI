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
   public $accountNumber;
   public $stepProcessed;
   public $lastResponse;
   public $isNewRequest;
   public $mobileNumber;
   public $created_at;
   public $clientCode;
   public $urlPrefix;
   public $sessionId;
   public $client_id;
   public $errorType;
   public $customer;
   public $district;
   public $response;
   public $mnoName;
   public $mno_id;
   public $status = 'INITIATED';
   public $clean;
   public $error;
   public $menu='Home';

   public function toSessionData():array{
      return [
               'id'=>$this->id,
               'sessionId'=>$this->sessionId,
               'mobileNumber'=>$this->mobileNumber,
               'mno_id'=>$this->mno_id,
               'client_id'=>$this->client_id,
               'menu'=>$this->menu,
               'customerJourney'=>$this->customerJourney, 
               'accountNumber'=>$this->accountNumber,
               'district'=>$this->district,
               'response'=>$this->response,
               'status'=>$this->status,
               'error'=>$this->error
         ];
   }
    
}
