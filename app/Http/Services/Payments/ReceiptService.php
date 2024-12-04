<?php

namespace App\Http\Services\Payments;

use App\Http\Services\Gateway\ConfirmPaymentSteps\Step_PostPaymentToClient;
use App\Http\Services\Gateway\ConfirmPaymentSteps\Step_SendReceiptViaSMS;
use App\Http\Services\Gateway\Utility\Step_UpdateTransaction;
use App\Http\Services\Gateway\Utility\Step_RefreshAnalytics; 
use App\Http\Services\Clients\BillingCredentialService;
use App\Http\Services\Payments\PaymentToReviewService;
use App\Http\Services\Gateway\Utility\Step_LogStatus;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Clients\ClientMnoService;
use App\Http\Services\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Carbon;
use App\Http\DTOs\MoMoDTO;
use Exception;

class ReceiptService
{

   public function __construct(
      private BillingCredentialService $billingCredentialService,
      private PaymentToReviewService $paymentToReviewService, 
      private ClientMenuService $clientMenuService,
      private ClientMnoService $clientMnoService,
      private MoMoDTO $paymentDTO)
   {}

   public function create(array $data):object|null{
      
      try {

         $thePayment = $this->paymentToReviewService->findById($data['id']);
         $paymentDTO = $this->paymentDTO->fromArray(\get_object_vars($thePayment));
         if($paymentDTO->paymentStatus != PaymentStatusEnum::Paid->value){
            throw new Exception("Unexpected payment status - ".$paymentDTO->paymentStatus);
         }

         if($paymentDTO->receiptNumber != ''){
            throw new Exception("Payment appears receipted! Receipt number ".$paymentDTO->receiptNumber);
         }

         if($paymentDTO->ppTransactionId == ''){
            throw new Exception("MNO transaction Id is null. Payment not yet confirmed!");
         }  

         //Bind Receipting Handler
            $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
            $theMenu = $this->clientMenuService->findById($paymentDTO->menu_id);
            $receiptingHandler = $theMenu->receiptingHandler;
            $billingClient = $theMenu->billingClient;
            if ($billpaySettings['USE_RECEIPTING_MOCK_'.strtoupper($paymentDTO->urlPrefix)] == "YES"){
               $receiptingHandler = "MockReceipting";
               $billingClient = "MockBillingClient";
            }
            App::bind(\App\Http\Services\External\BillingClients\IBillingClient::class,$billingClient);
            App::bind(\App\Http\Services\Gateway\ReceiptingHandlers\IReceiptPayment::class,$receiptingHandler);
         //
         
         if($user = Auth::user()){
            $paymentDTO->user_id = $user->id;
         }

         $paymentDTO->error = "";
         $paymentDTO =  App::make(Pipeline::class)
                        ->send($paymentDTO)
                        ->through(
                           [
                              Step_PostPaymentToClient::class,
                              Step_SendReceiptViaSMS::class,
                              Step_UpdateTransaction::class,  
                              Step_LogStatus::class,
                              Step_RefreshAnalytics::class
                           ]
                        )
                        ->thenReturn();
      
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $paymentDTO;
      
   }

}
