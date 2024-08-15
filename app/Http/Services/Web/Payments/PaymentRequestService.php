<?php

namespace App\Http\Services\Web\Payments;

use App\Http\Services\Web\Clients\ClientWalletService;
use App\Http\Services\USSD\StepServices\GetAmount;
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
      private GetAmount $getAmount,
      private SessionService $sessionService,
      private ClientService $clientService,
      private MnoService $mnoService,
      private WebDTO $webDTO
   ) {}


   public function initiateWebPayement(array $params, PaymentDTO $thePaymentDTO): array
   {

      try {

         $webDTO = $this->webDTO->fromArray($params);
         $webDTO->sessionId = 'WEB.'.$webDTO->customerAccount.'D'.\date('ymd').'T'.\date('His');
         $webDTO = $this->getMNO($webDTO);
         $webDTO = $this->getClientWallet($webDTO);
         $webDTO = $this->getClient($webDTO);
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
                           $paymentDTO->customerAccount.' - Amount: '.
                           $paymentDTO->paymentAmount
                        );

      } catch (\Throwable $e) {
         if($e->getCode()==1){
            throw new Exception("Invalid amount enterred.");
         }
         throw new Exception($e->getMessage());
      }
      return [
               'session_id' => $paymentDTO->session_id,
               'paymentStatusCheck' => (int)\env($paymentDTO->walletHandler.'_PAYSTATUS_CHECK'),
               'message' => \strtoupper($paymentDTO->urlPrefix)." Payment request submitted to ".$paymentDTO->walletHandler.
                                    ". You will receive a PIN prompt shortly!"
               ];

   }

   private function getMNO(WebDTO $webDTO) : WebDTO
   {

      try {
         $mnoName = MNOs::getMNO(substr($webDTO->mobileNumber,0,5));
         $mno = $this->mnoService->findOneBy(['name'=>$mnoName]);             
         $webDTO->mnoName = $mno->name;
         $webDTO->mno_id = $mno->id;
         return $webDTO;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

   private function getClientWallet(WebDTO $webDTO) : WebDTO
   {

      $clientWallet = $this->ClientWalletService->findById($webDTO->wallet_id);
      $webDTO->walletHandler = $clientWallet->handler;
      $webDTO->client_id = $clientWallet->client_id;
      if($clientWallet->paymentsMode != 'UP'){
         throw new Exception(\env('MODE_MESSAGE'));
      }
      if($clientWallet->paymentsActive != 'YES'){
         throw new Exception(\env('BLOCKED_MESSAGE')." ".strtoupper($webDTO->urlPrefix));
      }
      return $webDTO;

   }

   private function getClient(WebDTO $webDTO) : WebDTO
   {
      $client = $this->clientService->findById($webDTO->client_id);
      $webDTO->shortCode = $client->shortCode;
      $webDTO->urlPrefix = $client->urlPrefix;
      $webDTO->testMSISDN = $client->testMSISDN;
      $webDTO->clientSurcharge = $client->surcharge;
      return $webDTO;
   }




}


