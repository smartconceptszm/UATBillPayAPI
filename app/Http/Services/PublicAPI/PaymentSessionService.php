<?php

namespace App\Http\Services\PublicAPI;

use App\Http\Services\USSD\StepServices\GetRevenueCollectionDetails;
use App\Http\Services\External\BillingClients\EnquiryHandler;
use App\Http\Services\USSD\StepServices\GetAmount;
use App\Http\Services\Sessions\SessionService;
use App\Http\Services\Payments\PaymentService;
use App\Http\Services\Clients\MnoService;
use App\Http\Services\Enums\MNOs;
use App\Http\DTOs\WebDTO;
use Exception;

class PaymentSessionService
{

   public function __construct(
      private GetRevenueCollectionDetails $getRevenuePointAndCollector,
      private SessionService $sessionService,
      private EnquiryHandler $enquiryHandler,
      private PaymentService $paymentService,
      private MnoService $mnoService,
      private GetAmount $getAmount,
      private WebDTO $webDTO,
   )
   {}

   public function handle(array $data):array
   {

      try {

         $webDTO = $this->webDTO->fromArray($data);

         $webDTO = $this->enquiryHandler->handle($webDTO);

         $webDTO->sessionId = 'WEB.'.$webDTO->customerAccount.'D'.\date('ymd').'T'.\date('His');
         $webDTO = $this->getMNO($webDTO);
         $webDTO = $this->getRevenuePointAndCollector->handle($webDTO);
         //Get payment amount
         $webDTO->subscriberInput = $webDTO->paymentAmount;
         [$webDTO->subscriberInput,$webDTO->paymentAmount] = $this->getAmount->handle($webDTO);
         //Save record to session
         $session = $this->sessionService->create($webDTO->toSessionData());
         
         //Create Payment Record
         $data['subscriberInput'] = $webDTO->subscriberInput;
         $data['paymentAmount'] = $webDTO->paymentAmount;
         $data['session_id'] = $session->id;
         $data['sessionId'] = $webDTO->sessionId;
         $data['mno_id'] = $webDTO->mno_id;
         $payment = $this->paymentService->create($data);
         
         return [
                     'payment_id' => $payment->id,
                     "customerAccount" => $webDTO->customerAccount, 
                     "customerName" => $webDTO->customer['name'], 
                     "customerAddress" => $webDTO->customer['address'], 
                     "accountBalance" => $webDTO->customer['balance']
                     ];
         
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      

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


}


