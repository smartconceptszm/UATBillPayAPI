<?php

namespace App\Http\Services\Gateway;

use App\Http\Services\USSD\StepServices\GetRevenueCollectionDetails;
use App\Http\Services\Clients\PaymentsProviderCredentialService;
use App\Http\Services\Payments\PaymentToReviewService;
use App\Http\Services\Clients\ClientWalletService;
use App\Http\Services\USSD\StepServices\GetAmount;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Sessions\SessionService;
use App\Http\Services\Clients\ClientService;
use App\Http\Services\Clients\MnoService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Pipeline\Pipeline;
use App\Http\Services\Enums\MNOs;
use App\Jobs\InitiatePaymentJob;
use Illuminate\Support\Carbon;
use App\Http\DTOs\PaymentDTO;
use App\Http\DTOs\CardDTO;
use App\Http\DTOs\WebDTO;

use Exception;

class PaymentRequestService
{

   public function __construct(
      private PaymentsProviderCredentialService $paymentsProviderCredentialService,
      private GetRevenueCollectionDetails $getRevenuePointAndCollector,
      private PaymentToReviewService $paymentToReviewService,
      private ClientWalletService $ClientWalletService,
      private ClientMenuService $clientMenuService,
      private SessionService $sessionService,
      private ClientService $clientService,
      private MnoService $mnoService,
      private CardDTO $paymentDTO,
      private GetAmount $getAmount,
      private WebDTO $webDTO
   ) {}

   public function initiateMoMoWebPayement(array $params, PaymentDTO $thePaymentDTO): array{

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

         InitiatePaymentJob::dispatch($paymentDTO)
                           ->delay(Carbon::now()
                           ->addSeconds((int)$paymentsProviderCredentials[$paymentDTO->walletHandler.'_SUBMIT_PAYMENT']))
                           ->onQueue('high');

         Log::info('('.$paymentDTO->urlPrefix.') '.
                        'MoMo Web payment initiated: Phone: '.
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

   public function initiateCardWebPayement(array $params, PaymentDTO $thePaymentDTO): array
   {

      try {

         $webDTO = $this->webDTO->fromArray($params);
         $webDTO->sessionId = 'WEB.' . $webDTO->customerAccount . 'D' . date('ymd') . 'T' . date('His');
         $webDTO->mobileNumber = '260761028631';
         $webDTO->walletNumber = '260761028631';
         // Fetch and set MNO details
         $webDTO = $this->getMNO($webDTO);

         // Fetch and set client wallet information
         $webDTO = $this->getClientWallet($webDTO);

         // Fetch and set client details
         $webDTO = $this->getClient($webDTO);

         // Fetch revenue point and collector
         $webDTO = $this->getRevenuePointAndCollector->handle($webDTO);

         // Handle amount calculation
         $webDTO->subscriberInput = $webDTO->paymentAmount;
         [$webDTO->subscriberInput, $webDTO->paymentAmount] = $this->getAmount->handle($webDTO);

         // Create session and save it
         $session = $this->sessionService->create($webDTO->toSessionData());

         // Prepare paymentDTO
         $paymentDTO = $thePaymentDTO->fromArray($webDTO->toArray());
         $paymentDTO->session_id = $session->id;

         $paymentsProviderCredentials = $this->paymentsProviderCredentialService->getProviderCredentials($paymentDTO->payments_provider_id);

         //Bind the PaymentsProvider Client Wallet 
            $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
            $walletHandler = $paymentDTO->walletHandler;
            if( $billpaySettings['WALLET_USE_MOCK_'.strtoupper($paymentDTO->urlPrefix)] == 'YES'){
               $walletHandler = 'MockWallet';
            }
            App::bind(\App\Http\Services\External\PaymentsProviderClients\IPaymentsProviderClient::class,$walletHandler);
         //

         $paymentDTO  =  App::make(Pipeline::class)
                              ->send($paymentDTO)
                              ->through(
                                 [
                                    \App\Http\Services\Gateway\InitiatePaymentSteps\Step_GetPaymentAmounts::class,
                                    \App\Http\Services\Gateway\InitiatePaymentSteps\Step_CreatePaymentRecord::class,
                                    \App\Http\Services\Gateway\InitiatePaymentSteps\Step_SendPaymentsProviderRequest::class, 
                                    \App\Http\Services\Gateway\Utility\Step_UpdateTransaction::class,  
                                    \App\Http\Services\Gateway\Utility\Step_LogStatus::class,
                                    \App\Http\Services\Gateway\Utility\Step_DailyAnalytics::class,  
                                 ]
                              )
                              ->thenReturn();

         if(!empty($paymentDTO->error)){
            throw new Exception($paymentDTO->error);
         }

      } catch (\Throwable $e) {
         // Enhanced error logging
         Log::error('Error in initiateWebPayement: ' . $e->getMessage(), ['stack' => $e->getTraceAsString()]);
         if ($e->getCode() == 1) {
            throw new Exception("Invalid amount entered.");
         }
         throw new Exception($e->getMessage());
      }

      return ['transactionId' => $paymentDTO->transactionId];
   }

   public function confirmWebPayment(string $transactionId) : object {

      $thePayment = $this->paymentToReviewService->findByTransactionId($transactionId);
      $paymentDTO = $this->paymentDTO->fromArray(\get_object_vars($thePayment));
      try {
      
         $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
         //Bind the PaymentsProvider Client Wallet 
            $walletHandler = $paymentDTO->walletHandler;
            if( $billpaySettings['WALLET_USE_MOCK_'.strtoupper($paymentDTO->urlPrefix)] == 'YES'){
               $walletHandler = 'MockWallet';
            }
            App::bind(\App\Http\Services\External\PaymentsProviderClients\IPaymentsProviderClient::class,$walletHandler);
         //
         // Bind Receipting and Billing Handlers
            $theMenu = $this->clientMenuService->findById($paymentDTO->menu_id);
            $receiptingHandler = $theMenu->receiptingHandler;
            $billingClient = $theMenu->billingClient;
            if ($billpaySettings['USE_RECEIPTING_MOCK_' . strtoupper($paymentDTO->urlPrefix)] == "YES") {
               $receiptingHandler = "MockReceipting";
            }
            if ($billpaySettings['USE_BILLING_MOCK_' . strtoupper($paymentDTO->urlPrefix)] == "YES") {
               $billingClient = "MockBillingClient";
            }
            App::bind(\App\Http\Services\External\BillingClients\IBillingClient::class, $billingClient);
            App::bind(\App\Http\Services\Gateway\ReceiptingHandlers\IReceiptPayment::class, $receiptingHandler);
         //

         $paymentDTO = App::make(Pipeline::class)
                           ->send($paymentDTO)
                           ->through([
                              \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_GetPaymentStatus::class,
                              \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_CheckReceiptStatus::class,
                              \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_PostPaymentToClient::class
                           ])
                           ->thenReturn();
         if($paymentDTO->paymentStatus == PaymentStatusEnum::Receipted->value){
            $paymentDTO->paymentStatus = PaymentStatusEnum::Receipt_Delivered->value;
         }
         $paymentDTO = App::make(Pipeline::class)
                           ->send($paymentDTO)
                           ->through([
                              \App\Http\Services\Gateway\Utility\Step_UpdateTransaction::class,
                              \App\Http\Services\Gateway\Utility\Step_LogStatus::class,
                              \App\Http\Services\Gateway\Utility\Step_RefreshAnalytics::class
                           ])
                           ->thenReturn();
      } catch (\Exception $e) {
         throw new Exception($e->getMessage(), 1);
      }
      return $paymentDTO;
      
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
         throw new Exception($clientWallet->modeMessage);
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


