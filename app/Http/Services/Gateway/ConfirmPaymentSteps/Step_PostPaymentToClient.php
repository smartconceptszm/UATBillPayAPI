<?php

namespace App\Http\Services\Gateway\ConfirmPaymentSteps;

use App\Http\Services\Clients\PaymentsProviderCredentialService;
use App\Http\Services\External\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Clients\ClientWalletService;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Cache;
use App\Jobs\PaymentsAnalyticsRegular;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class Step_PostPaymentToClient extends EfectivoPipelineContract
{

   public function __construct(
      private PaymentsProviderCredentialService $paymentsProviderCredentialService,
      private ClientWalletService $clientWalletService,
      private IReceiptPayment $receiptPayment)
   {}

   protected function stepProcess(BaseDTO $paymentDTO)
   {
      
      try {
         if(($paymentDTO->paymentStatus == 'PAID | NOT RECEIPTED') || ($paymentDTO->paymentStatus == 'PAID | NO TOKEN')){
            $paymentDTO = $this->receiptPayment->handle($paymentDTO);
            //Analytics Refresh
               $billpaySettings = \json_decode(Cache::get('billpaySettings',\json_encode([])), true);
               $dashboardCache = (int)$billpaySettings['DASHBOARD_CACHE']; 
               $clientPaymentCount = (int)Cache::get($paymentDTO->client_id.'_PaymentStatusCount');
               if(($clientPaymentCount + 1) == $dashboardCache){
                  $clientPaymentCount = (int)Cache::get($paymentDTO->client_id.'_PaymentStatusCount');
                  $clientWallet = $this->clientWalletService->findById($paymentDTO->wallet_id);
                  $paymentsProviderCredentials = $this->paymentsProviderCredentialService->getProviderCredentials($clientWallet->payments_provider_id);
                  Queue::later(Carbon::now()->addSeconds((int)$paymentsProviderCredentials[$paymentDTO->walletHandler.'_PAYSTATUS_CHECK']),new PaymentsAnalyticsRegular($paymentDTO),'','high');
                  Cache::put($paymentDTO->client_id.'_PaymentStatusCount', 1, Carbon::now()->addMinutes((int)$billpaySettings['DASHBOARD_CACHE']));
               }else{
                  Cache::increment($paymentDTO->client_id.'_PaymentStatusCount');
               }
            //
         }
      } catch (\Throwable $e) {
         $paymentDTO->error='At post payment step. '.$e->getMessage();
      }
      return  $paymentDTO;
      
   }

}