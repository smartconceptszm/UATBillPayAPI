<?php

namespace App\Http\DTOs;


/**
 * @property $id
 * @property string $response
 * @property string $error
 */

class WebDTO extends BaseDTO
{

   public $payments_provider_id;
   public $revenueCollector;
   public $customerAccount;
   public $subscriberInput;
   public $clientSurcharge;
   public $paymentAmount;
   public $walletHandler;
   public $billingClient;
   public $consumerTier;
   public $revenuePoint;
   public $consumerType;
   public $mobileNumber;
   public $walletNumber;
   public $testMSISDN;
   public $created_at;
   public $updated_at;
   public $urlPrefix;
   public $sessionId;
   public $wallet_id;
   public $client_id;
   public $reference;
   public $mnoName;
   public $handler;
   public $channel;
   public $menu_id;
   public $mno_id;
   public $status = 'INITIATED';
   public $error;

   public $cardHolderName;
   public $cardExpiry;
   public $cardCVV;

   public function toSessionData():array{
      return [
            'customerAccount'=>$this->customerAccount,
            'paymentAmount'=>$this->paymentAmount,
            'mobileNumber'=>$this->mobileNumber,
            'consumerTier'=>$this->consumerTier,
            'revenuePoint'=>$this->revenuePoint,
            'consumerType'=>$this->consumerType,
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
