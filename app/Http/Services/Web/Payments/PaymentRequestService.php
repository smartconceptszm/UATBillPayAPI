<?php

namespace App\Http\Services\Web\Payments;

use App\Http\Services\Web\Clients\ClientWalletService;
use App\Http\Services\USSD\Utility\StepService_GetAmount;
use App\Http\Services\Web\Sessions\SessionService;
use App\Http\Services\Web\Clients\ClientService;
use App\Http\Services\Web\Clients\MnoService;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Log;
use App\Http\Services\Enums\MNOs;
use App\Jobs\InitiatePaymentJob;
use Illuminate\Support\Carbon;
use App\Http\DTOs\PaymentDTO;
use App\Http\DTOs\WebDTO;
use Exception;

class PaymentRequestService
{

   public function __construct(
      private ClientWalletService $ClientWalletService,
      private StepService_GetAmount $getAmount,
      private SessionService $sessionService,
      private ClientService $clientService,
      private MnoService $mnoService,
      private WebDTO $webDTO
   ) {}


   public function initiateWebPayement(array $params, PaymentDTO $thePaymentDTO): string
   {

      try {

         $webDTO = $this->webDTO->fromArray($params);
         $webDTO->sessionId = 'WEB.'.$webDTO->accountNumber.'D'.\date('ymd').'T'.\date('His');
         $webDTO = $this->getMNO($webDTO);
         $webDTO = $this->getClient($webDTO);
         $webDTO = $this->getClientWallet($webDTO);
         //Get payment amount
         $webDTO->subscriberInput = $webDTO->paymentAmount;
         [$webDTO->subscriberInput,$webDTO->paymentAmount] = $this->getAmount->handle($webDTO);
         //Save record to session
         $session = $this->sessionService->create($webDTO->toSessionData());
         //Initiate Payment
         $paymentDTO = $thePaymentDTO->fromArray($webDTO->toArray());
         $paymentDTO->session_id = $session->id;
         Queue::later(Carbon::now()->addSeconds((int)\env($paymentDTO->walletHandler.
                     '_SUBMIT_PAYMENT')), new InitiatePaymentJob($paymentDTO),'','high');

         Log::info('('.$paymentDTO->urlPrefix.') '.
                        'Web payment initiated: Phone: '.
                           $paymentDTO->mobileNumber.' - Account Number: '.
                           $paymentDTO->accountNumber.' - Meter Number: '.
                           $paymentDTO->meterNumber.' - Amount: '.
                           $paymentDTO->paymentAmount
                        );

      } catch (\Throwable $e) {
         if($e->getCode()==1){
            throw new Exception("Invalid amount enterred.");
         }
         throw new Exception($e->getMessage());
      }
      return \strtoupper($paymentDTO->urlPrefix)." Payment request submitted to ".$paymentDTO->mnoName.
                  ". You will receive a PIN prompt shortly!";

   }

   private function getMNO(WebDTO $webDTO) : WebDTO
   {

      try {
         $mnoName = MNOs::getMNO(substr($webDTO->mobileNumber,5));
         $mno = $this->mnoService->findOneBy(['name'=>$mnoName]); 
         $mno = \is_null($mno)?null:(object)$mno->toArray();              
         $webDTO->mnoName = $mno->name;
         $webDTO->mno_id = $mno->id;
         return $webDTO;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

   private function getClient(WebDTO $webDTO) : WebDTO
   {
      $client = $this->clientService->findById($webDTO->client_id);
      $client = \is_null($client)?null:(object)$client->toArray();
      $webDTO->shortCode = $client->shortCode;
      $webDTO->urlPrefix = $client->urlPrefix;
      $webDTO->testMSISDN = $client->testMSISDN;
      $webDTO->clientSurcharge = $client->surcharge;
      return $webDTO;
   }

   private function getClientWallet(WebDTO $webDTO) : WebDTO
   {

      $clientWallet = $this->ClientWalletService->findOneBy([
                                          'payments_provider_id' => $$webDTO->payments_provider_id,
                                          'client_id' => $webDTO->client_id
                                       ]);
      $clientWallet = \is_null($clientWallet)?null:(object)$clientWallet->toArray();
      $webDTO->walletHandler = $clientWallet->handler;
      $webDTO->wallet_id = $clientWallet->id;
      if($clientWallet->paymentsMode != 'UP'){
         throw new Exception(\env('MODE_MESSAGE'));
      }
      if($clientWallet->paymentsActive != 'YES'){
         throw new Exception(\env('BLOCKED_MESSAGE')." ".strtoupper($webDTO->urlPrefix));
      }
      return $webDTO;

   }


}


