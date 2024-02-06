<?php

namespace App\Http\Services\Web;

use App\Http\Services\External\BillingClients\GetCustomerAccount;
USE App\Http\Services\USSD\Utility\StepService_GetAmount;
USE App\Http\Services\Clients\ClientService;
USE App\Http\Services\USSD\SessionService;
USE App\Http\Services\Clients\MnoService;
use Illuminate\Support\Facades\Queue;
use App\Jobs\InitiateMoMoPaymentJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Http\DTOs\MoMoDTO;
use App\Http\DTOs\WebDTO;
use Exception;

class WebPaymentService
{

   public function __construct(
      private GetCustomerAccount $getCustomerAccount,
      private StepService_GetAmount $getAmount,
      private SessionService $sessionService,
      private ClientService $clientService,
      private MnoService $mnoService,
      private MoMoDTO $moMoDTO,
      private WebDTO $webDTO,
   )
   {}

   public function getCustomer(string $accountNumber, string $urlPrefix):array
   {

      try {
         $momoDTO = $this->moMoDTO->fromArray([
                                             'accountNumber'=> $accountNumber,
                                             'urlPrefix'=>$urlPrefix
                                          ]);
         $momoDTO = $this->getCustomerAccount->handle($momoDTO);
         return $momoDTO->customer;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

   public function initiateWebPayement(array $params): string
   {

      try {

         $webDTO = $this->webDTO->fromArray($params);
         $webDTO->sessionId = 'WEB.'.$webDTO->accountNumber.'D'.\date('ymd').'T'.\date('His');
         
         $webDTO = $this->getClient($webDTO);
         $webDTO = $this->getMNO($webDTO);
         //Get payment amount
         $webDTO->subscriberInput=$webDTO->paymentAmount;
         [$webDTO->subscriberInput,$webDTO->paymentAmount] = $this->getAmount->handle($webDTO);
         //Save record to session
         $session = $this->sessionService->create($webDTO->toSessionData());
         //Initiate MoMo Payment
         $momoDTO = $this->moMoDTO->fromArray($webDTO->toArray());
         $momoDTO->session_id = $session->id;
         Queue::later(Carbon::now()->addSeconds((int)\env($momoDTO->mnoName.
                     '_SUBMIT_PAYMENT')), new InitiateMoMoPaymentJob($momoDTO));

         Log::info('('.$momoDTO->urlPrefix.') '.
            'Web payment initiated: Phone: '.
               $momoDTO->mobileNumber.' - Account Number: '.
               $momoDTO->accountNumber.' - Amount: '.
               $momoDTO->paymentAmount
            );

      } catch (\Throwable $e) {
         if($e->getCode()==1){
            throw new Exception("Invalid amount enterred.");
         }
         throw new Exception($e->getMessage());
      }
      return \strtoupper($momoDTO->urlPrefix)." Payment request submitted to ".$momoDTO->mnoName.
                  ". You will receive a PIN prompt shortly!";

   }

   private function getClient(WebDTO $webDTO) : WebDTO
   {
      $client = $this->clientService->findOneBy(['urlPrefix'=>$webDTO->urlPrefix]);
      $webDTO->client_id = $client->id;
      $webDTO->shortCode = $client->shortCode;
      $webDTO->testMSISDN = $client->testMSISDN;
      $webDTO->clientSurcharge = $client->surcharge;
      if($client->mode != 'UP'){
         throw new Exception(\env('MODE_MESSAGE'));
      }
      if($client->status != 'ACTIVE'){
         throw new Exception(\env('BLOCKED_MESSAGE')." ".strtoupper($webDTO->urlPrefix));
      }
      return $webDTO;
   }

   private function getMNO(WebDTO $webDTO) : WebDTO
   {

      try {
         $mno = $this->mnoService->findById($webDTO->mno_id);               
         $webDTO->mnoName = $mno->name;
         return $webDTO;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

}
