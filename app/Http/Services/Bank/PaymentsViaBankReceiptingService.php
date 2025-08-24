<?php

namespace App\Http\Services\Bank;

use App\Http\Services\External\BillingClients\EnquiryHandler;
use App\Http\Services\Utility\SCLExternalServiceBinder;
use App\Http\Services\Clients\PaymentsProviderService;
use App\Http\Services\Clients\ClientWalletService;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Http\Services\Clients\ClientService;

use App\Http\Services\Clients\ClientMenuService;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Pipeline\Pipeline;

use App\Http\DTOs\BankDTO;

use Exception;

class PaymentsViaBankReceiptingService
{

   public function __construct(
      private SCLExternalServiceBinder $sclExternalServiceBinder,
      private PaymentsProviderService $paymentsProviderService,
      private ClientWalletService $clientWalletService,
      private ClientMenuService $clientMenuService,
      private EnquiryHandler $enquiryHandler,
      private ClientService $clientService,
      private BankDTO $bankDTO
   ) {}

   public function handle(array $params): array{

      try {

         $bankDTO = $this->bankDTO->fromSessionData($params);

         $bankDTO = $this->getClient($bankDTO);
         $bankDTO = $this->getClientMenu($bankDTO);
         $bankDTO = $this->getClientWallet($bankDTO);

         $bankDTO->paymentStatus = PaymentStatusEnum::Paid->value;

         $bankDTO = $this->enquiryHandler->handle($bankDTO);

         if($bankDTO->compositeAccount){
            $bankDTO  =  App::make(Pipeline::class)
                              ->send($bankDTO)
                              ->through(
                                 [
                                    \App\Http\Services\Gateway\InitiatePaymentSteps\Step_CreatePaymentRecord::class,
                                    \App\Http\Services\Gateway\Utility\Step_LogStatusInfoOnly::class,
                                 ]
                              )
                              ->thenReturn();
            $bankDTO->receipt = "Payment received by ".strtoupper($bankDTO->urlPrefix). 
                                          ". Log on to payments.smartconcepts.co.zm/".$bankDTO->urlPrefix.
                                          "/receiptcomposite to allocate and receipt the funds to your ".
                                          strtoupper($bankDTO->urlPrefix)." accounts";
         }else{

            // Bind Billing and Receipting Handlers
               $this->sclExternalServiceBinder->bindBillingAndReceipting($bankDTO->urlPrefix,$bankDTO->menu_id);
            //

            $bankDTO  =  App::make(Pipeline::class)
                              ->send($bankDTO)
                              ->through(
                                 [
                                    \App\Http\Services\Gateway\InitiatePaymentSteps\Step_CreatePaymentRecord::class,
                                    \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_PostPaymentToClient::class,
                                    \App\Http\Services\Gateway\Utility\Step_UpdateTransaction::class,
                                    \App\Http\Services\Gateway\Utility\Step_LogStatusAll::class,

                                    \App\Http\Services\Gateway\Utility\Step_RefreshAnalytics::class
                                 ]
                              )
                              ->thenReturn();
         }

      } catch (\Throwable $e) {
         if($e->getCode()==1){
            throw new Exception("Invalid amount enterred.");
         }
         throw new Exception($e->getMessage());
      }

      return [
               'message' => str_replace(["\r\n", "\r", "\n"], ' ', $bankDTO->receipt)
            ];

   }

   private function getClient(BankDTO $bankDTO) : BankDTO
   {
      $client = $this->clientService->findOneBy(['urlPrefix'=>$bankDTO->urlPrefix]);
      $bankDTO->clientSurcharge = $client->surcharge;
      $bankDTO->testMSISDN = $client->testMSISDN;
      $bankDTO->shortCode = $client->shortCode;
      $bankDTO->client_id = $client->id;
      return $bankDTO;
   }

   private function getClientMenu(BankDTO $bankDTO) : BankDTO
   {

      $menu = $this->clientMenuService->findOneBy([   'client_id' => $bankDTO->client_id,
                                                      'paymentType'=>"POST-PAID",
                                                      'isPayment' => "YES",
                                                      'isDefault' => "YES",
                                                      'isActive' => "YES",
                                                   ]);
      $bankDTO->menu_id = $menu->id;
      return $bankDTO;

   }

   private function getClientWallet(BankDTO $bankDTO) : BankDTO
   {

      $user = Auth::user(); 
      $paymentsProvider = $this->paymentsProviderService->findOneBy(['client_id'=>$user->client_id]);
      $clientWallet = $this->clientWalletService->findOneBy([
                                                      'payments_provider_id' => $paymentsProvider->id,
                                                      'client_id' => $bankDTO->client_id 
                                                   ]);
      $bankDTO->payments_provider_id = $paymentsProvider->id;
      $bankDTO->walletHandler = $clientWallet->handler;
      $bankDTO->wallet_id = $clientWallet->id;
      if($clientWallet->paymentsMode != 'UP'){
         throw new Exception($clientWallet->modeMessage);
      }
      if($clientWallet->paymentsActive != 'YES'){
         $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
         throw new Exception($billpaySettings['BLOCKED_MESSAGE']." ".strtoupper($bankDTO->urlPrefix));
      }
      return $bankDTO;

   }

}

