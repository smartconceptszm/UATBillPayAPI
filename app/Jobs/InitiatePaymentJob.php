<?php

namespace App\Jobs;

use App\Http\Services\Gateway\InitiatePayment;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;
use App\Jobs\BaseJob;

class InitiatePaymentJob extends BaseJob
{

   // public $timeout = 600;
   public function __construct(
      private BaseDTO $paymentDTO)
   {}

   public function handle(InitiatePayment $initiatePayment)
   {
      
      //Bind the PaymentsProvider Client Wallet 
      $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
      $walletHandler = $this->paymentDTO->walletHandler;
      if( $billpaySettings['WALLET_USE_MOCK'] == 'YES'){
         $walletHandler = 'MockWallet';
      }
      App::bind(\App\Http\Services\External\PaymentsProviderClients\IPaymentsProviderClient::class,$walletHandler);
      //Handle the Job
      return $initiatePayment->handle($this->paymentDTO);

   }

}