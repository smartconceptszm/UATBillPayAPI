<?php

namespace App\Http\Services\Web;

use App\Http\Services\External\BillingClients\GetCustomerAccount;
USE App\Http\Services\Clients\ClientService;
USE App\Http\Services\Clients\MnoService;
use Illuminate\Support\Facades\Queue;
use App\Jobs\InitiateMoMoPaymentJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Http\DTOs\MoMoDTO;
use Exception;

class WebPaymentService
{

   public function __construct(
      private GetCustomerAccount $getCustomerAccount,
      private ClientService $clientService,
      private MnoService $mnoService,
      private MoMoDTO $moMoDTO
   )
   {}

   public function getCustomer(string $accountNumber, string $urlPrefix):array
   {

      try {
         $momoDTO = $this->moMoDTO->fromArray([
                                             'accountNumber'=> $accountNumber,
                                             'urlPrefix'=>$urlPrefix
                                          ]);
         return $this->getCustomerAccount->handle($momoDTO);
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

   public function initiateWebPayement(array $params): string
   {

      try {
         $momoDTO = $this->moMoDTO->fromArray($params);
         $momoDTO = $this->getClient($momoDTO);
         $momoDTO = $this->getMNO($momoDTO);
         Queue::later(Carbon::now()->addSeconds((int)\env($momoDTO->mnoName.
                     '_SUBMIT_PAYMENT')), new InitiateMoMoPaymentJob($momoDTO));

         Log::info('('.$momoDTO->urlPrefix.') '.
            'Web payment initiated: Phone: '.
               $momoDTO->mobileNumber.' - Account Number: '.
               $momoDTO->accountNumber.' - Amount: '.
               $momoDTO->paymentAmount
            );

      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return \strtoupper($momoDTO->urlPrefix)." Payment request submitted to ".$momoDTO->mnoName.
                  ". You will receive a PIN prompt shortly!";

   }

   private function getClient(MoMoDTO $momoDTO) : MoMoDTO
   {
      $client = $this->clientService->findOneBy(['urlPrefix'=>$momoDTO->urlPrefix]);
      $momoDTO->client_id = $client->id;
      $momoDTO->shortCode = $client->shortCode;
      $momoDTO->testMSISDN = $client->testMSISDN;
      $momoDTO->clientSurcharge = $client->surcharge;
      if($client->mode != 'UP'){
         throw new Exception(\env('MODE_MESSAGE'));
      }
      if($client->status != 'ACTIVE'){
         throw new Exception(\env('BLOCKED_MESSAGE')." ".strtoupper($momoDTO->urlPrefix));
      }
      return $momoDTO;
   }

   private function getMNO(MoMoDTO $momoDTO) : MoMoDTO
   {

      try {
         $mno = $this->mnoService->findById($momoDTO->mno_id);               
         $momoDTO->mnoName = $mno->name;
         return $momoDTO;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

}
