<?php

namespace App\Http\Services\Web;

use App\Http\Services\MoMo\Utility\StepService_CalculatePaymentAmounts;
use App\Http\Services\External\BillingClients\GetCustomerAccount;
use App\Http\Services\Payments\PaymentService;
USE App\Http\Services\Clients\ClientService;
USE App\Http\Services\Clients\MnoService;
use Illuminate\Support\Facades\App;
use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\MoMoDTO;
use Exception;

class WebPaymentService
{

   public function __construct(
      private StepService_CalculatePaymentAmounts $calculatePaymentAmounts,
      private GetCustomerAccount $getCustomerAccount,
      private PaymentService $paymentService,
      private ClientService $clientService,
      private MnoService $mnoService,
      private MoMoDTO $moMoDTO
   )
   {}

   public function getCustomer(string $accountNumber):array
   {

      try {
         return $this->getCustomerAccount->handle($accountNumber,'swasco');
      } catch (Exception $e) {
         throw new Exception($e->getMessage());
      }

   }

   public function initiateWebPayement(array $params): string
   {

      try {
         $momoDTO = $this->getClient($this->moMoDTO->fromArray($params));
         $momoDTO = $this->getMNO($momoDTO);

         $calculatedAmounts = $this->calculatePaymentAmounts->handle(
                                    $momoDTO->urlPrefix,$momoDTO->mno_id,$momoDTO->paymentAmount);

         $momoDTO->surchargeAmount = $calculatedAmounts['surchargeAmount'];
         $momoDTO->receiptAmount = $calculatedAmounts['receiptAmount'];
         $momoDTO->paymentAmount = $calculatedAmounts['paymentAmount'];
         $momoDTO->clientCode = $calculatedAmounts['clientCode'];
         $momoDTO = $this->createPaymentRecord($momoDTO);
         //Process the request
         $momoDTO  =  app(Pipeline::class)
            ->send($momoDTO)
            ->through(
               [
                  \App\Http\Services\MoMo\InitiatePaymentSteps\Step_SendMoMoRequest::class, 
                  \App\Http\Services\MoMo\InitiatePaymentSteps\Step_DispatchConfirmationJob::class,
                  \App\Http\Services\MoMo\Utility\Step_UpdateTransaction::class,  
                  \App\Http\Services\MoMo\Utility\Step_LogStatus::class 
               ]
            )
            ->thenReturn();

      } catch (Exception $e) {
         # code...
      }
      return \strtoupper($momoDTO->urlPrefix)." Payment request submitted to ".$momoDTO->mnoName."\n".
                  "You will receive a PIN prompt shortly!"."\n\n";

   }

   private function getClient(MoMoDTO $moMoDTO) : MoMoDTO
   {
      $client = $this->clientService->findOneBy(['urlPrefix'=>$moMoDTO->urlPrefix]);
      $moMoDTO->client_id = $client->id;
      $moMoDTO->clientCode = $client->code;
      $moMoDTO->clientSurcharge = $client->surcharge;
      if($client->mode != 'UP'){
         $moMoDTO->error = 'System in Maintenance Mode';
         $moMoDTO->errorType = "MaintenanceMode";
         return $moMoDTO;
      }
      if($client->status != 'ACTIVE'){
         $moMoDTO->error = 'Client is blocked';
         $moMoDTO->errorType = "ClientBlocked";
         return $moMoDTO;
      }
   }

   private function getMNO(MoMoDTO $moMoDTO) : MoMoDTO
   {

      $mno = $this->mnoService->findOneBy(['name'=>$moMoDTO->mnoName]);               
      $moMoDTO->mno_id = $mno->id;
      App::bind(\App\Http\Services\External\MoMoClients\IMoMoClient::class,$moMoDTO->mnoName);
      return $moMoDTO;
   }

   private function createPaymentRecord(MoMoDTO $moMoDTO) : MoMoDTO
   {
      $payment = $this->paymentService->create($moMoDTO->toPaymentData());
      $moMoDTO->id = $payment->status;
      $moMoDTO->id = $payment->id;
      return $moMoDTO;
   }


}
