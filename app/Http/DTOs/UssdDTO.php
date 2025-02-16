<?php

namespace App\Http\DTOs;


/**
 * @property $id
 * @property string $response
 * @property string $error
 */

class UssdDTO extends BaseDTO
{

   public $payments_provider_id;
   public $revenueCollector;
   public $clientSurcharge;
   public $subscriberInput;
   public $fireMoMoRequest;
   public $customerJourney;
   public $customerAccount;
   public $ussdAggregator;
   public $paymentAmount;
   public $billingClient;
   public $walletHandler;
   public $consumerTier;
   public $revenuePoint;
   public $consumerType;
   public $lastResponse;
   public $isNewRequest;
   public $mobileNumber;
   public $testMSISDN;
   public $menuPrompt;
   public $created_at;
   public $updated_at;
   public $urlPrefix;
   public $sessionId;
   public $wallet_id;
   public $client_id;
   public $reference;
   public $errorType;
   public $shortCode;
   public $customer;
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
            'customerAccount'=>$this->customerAccount,
            'paymentAmount'=>$this->paymentAmount,
            'consumerTier'=>$this->consumerTier,
            'revenuePoint'=>$this->revenuePoint,
            'consumerType'=>$this->consumerType,
            'mobileNumber'=>$this->mobileNumber,
            'sessionId'=>$this->sessionId,
            'client_id'=>$this->client_id,
            'response'=>$this->response,
            'menu_id'=>$this->menu_id,
            'mno_id'=>$this->mno_id,
            'status'=>$this->status,
            'error'=>$this->error,
            'id'=>$this->id
         ];
   }
    
}
