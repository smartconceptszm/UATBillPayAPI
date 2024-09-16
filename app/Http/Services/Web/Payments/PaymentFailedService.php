<?php

namespace App\Http\Services\Web\Payments;

use App\Http\Services\Web\Clients\BillingCredentialService;
use App\Http\Services\Web\Payments\PaymentToReviewService;
use App\Http\Services\Web\Clients\ClientMenuService;
use App\Http\Services\Web\Clients\ClientMnoService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\MoMoDTO;
use Exception;

class PaymentFailedService
{

   public function __construct(
      private BillingCredentialService $billingCredentialService,
      private PaymentToReviewService $paymentToReviewService,
      private ClientMenuService $clientMenuService,
      private ClientMnoService $clientMnoService,
      private MoMoDTO $paymentDTO)
   {}

   public function findAll(array $criteria):array|null{
      try {
         $user = Auth::user(); 
         $criteria['client_id'] = $user->client_id;
         $dto=(object)$criteria;
         $dto->dateFrom = $dto->dateFrom." 00:00:00";
         $dto->dateTo = $dto->dateTo." 23:59:59";
         $records = DB::table('payments as p')
                  ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                  ->join('payments_providers as pp','cw.payments_provider_id','=','pp.id')
                  ->join('clients as c','cw.client_id','=','c.id')
                  ->join('sessions as s','p.session_id','=','s.id')
                  ->join('client_menus as m','p.menu_id','=','m.id')
                  ->select('p.*','m.prompt as paymentType','pp.shortName as paymentProvider');
         if($dto->dateFrom && $dto->dateTo){
            $records =$records->whereBetween('p.created_at',[$dto->dateFrom, $dto->dateTo]);
         }
         $allFailed = $records->where('c.id', '=', $dto->client_id)
                              ->whereIn('p.paymentStatus', ['SUBMISSION FAILED','PAYMENT FAILED'])
                              ->orderByDesc('p.created_at');
         $allFailed = $records->get();
         $providerErrors = $allFailed->filter(
               function ($item) {
                  if (
                        ((\strpos($item->error,"Status Code")) || (\strpos($item->error,"on get transaction status")) 
                           || (\strpos($item->error,"Token error")) || (\strpos($item->error,"on collect funds")))
                        ||
                        ($item->paymentStatus == "SUBMISSION FAILED")   
                     ) 
                  {
                        return $item;
                  }
               }
            )->values();
         return [
               'all' => $allFailed,
               'providerErrors' => $providerErrors,
            ];

      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

   public function update(string $id):string{

      try {
         
         $thePayment = $this->paymentToReviewService->findById($id);
         $paymentDTO = $this->paymentDTO->fromArray(\get_object_vars($thePayment));
         if($user = Auth::user()){
            $paymentDTO->user_id = $user->id;
         } 
         $paymentDTO->error = "";

         //Bind the PaymentsProviderClient
            $walletHandler = $paymentDTO->walletHandler;
            if(\env("WALLET_USE_MOCK") == 'YES'){
               $walletHandler = 'MockWallet';
            }
            App::bind(\App\Http\Services\External\PaymentsProviderClients\IPaymentsProviderClient::class,$walletHandler);
         //

         //Bind Receipting Handler
            $theMenu = $this->clientMenuService->findById($paymentDTO->menu_id);
            $receiptingHandler = $theMenu->receiptingHandler;
            $billingClient = $theMenu->billingClient;
            if (\env('USE_RECEIPTING_MOCK') == "YES"){
               $receiptingHandler = "MockReceipting";
               $billingClient = "MockBillingClient";
            }
            App::bind(\App\Http\Services\External\BillingClients\IBillingClient::class,$billingClient);
            App::bind(\App\Http\Services\External\ReceiptingHandlers\IReceiptPayment::class,$receiptingHandler);
         //
         
         //Reconfirm/Review the payment Transaction
            $paymentDTO =  App::make(Pipeline::class)
                  ->send($paymentDTO)
                  ->through(
                     [
                        \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_GetPaymentStatus::class,
                        \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_CheckReceiptStatus::class,
                        \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_PostPaymentToClient::class,
                        \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_SendReceiptViaSMS::class,
                        \App\Http\Services\Gateway\Utility\Step_UpdateTransaction::class,  
                        \App\Http\Services\Gateway\Utility\Step_LogStatus::class 
                     ]
                  )
                  ->thenReturn();
            if($paymentDTO->error==''){
               $logMessage = $paymentDTO->receipt;
            }else{
               $logMessage = $paymentDTO->error;
            }
            return $logMessage;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
