<?php

namespace App\Http\Services\Gateway;

use App\Http\Services\Clients\PaymentsProviderCredentialService;
use App\Http\Services\USSD\StepServices\GetRevenueCollectionDetails;
use App\Http\Services\Clients\ClientWalletService;
use App\Http\Services\USSD\StepServices\GetAmount;
use App\Http\Services\Sessions\SessionService;
use App\Http\Services\Clients\ClientService;
use App\Http\Services\Clients\MnoService;
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
      private PaymentsProviderCredentialService $paymentsProviderCredentialService,
      private GetRevenueCollectionDetails $getRevenuePointAndCollector,
      private ClientWalletService $ClientWalletService,
      private SessionService $sessionService,
      private ClientService $clientService,
      private MnoService $mnoService,
      private GetAmount $getAmount,
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
         $webDTO = $this->getRevenuePointAndCollector->handle($webDTO);
         //Get payment amount
         $webDTO->subscriberInput = $webDTO->paymentAmount;
         [$webDTO->subscriberInput,$webDTO->paymentAmount] = $this->getAmount->handle($webDTO);
         //Save record to session
         $session = $this->sessionService->create($webDTO->toSessionData());
         //Initiate Payment
         $paymentDTO = $thePaymentDTO->fromArray($webDTO->toArray());
         $paymentDTO->session_id = $session->id;
         $paymentsProviderCredentials = $this->paymentsProviderCredentialService->getProviderCredentials($paymentDTO->payments_provider_id);
      
         Queue::later(Carbon::now()->addSeconds((int) $paymentsProviderCredentials[$paymentDTO->walletHandler.
                     '_SUBMIT_PAYMENT']), new InitiatePaymentJob($paymentDTO),'','high');

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
               'paymentStatusCheck' => (int) $paymentsProviderCredentials[$paymentDTO->walletHandler.'_PAYSTATUS_CHECK'],
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

      $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
      $clientWallet = $this->ClientWalletService->findById($webDTO->wallet_id);
      $webDTO->payments_provider_id = $clientWallet->payments_provider_id;
      $webDTO->walletHandler = $clientWallet->handler;
      $webDTO->client_id = $clientWallet->client_id;
      if($clientWallet->paymentsMode != 'UP'){
         throw new Exception($billpaySettings['MODE_MESSAGE']);
      }
      if($clientWallet->paymentsActive != 'YES'){
         throw new Exception($billpaySettings['BLOCKED_MESSAGE']." ".strtoupper($webDTO->urlPrefix));
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


