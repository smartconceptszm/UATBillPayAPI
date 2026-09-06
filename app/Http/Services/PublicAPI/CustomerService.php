<?php

namespace App\Http\Services\PublicAPI;

use App\Http\Services\External\BillingClients\EnquiryHandler;
use Illuminate\Support\Facades\Auth;
use App\Http\DTOs\MoMoDTO;
use Exception;

class CustomerService
{

   public function __construct(
      private EnquiryHandler $enquiryHandler,
      private MoMoDTO $momoDTO,
   )
   {}

   public function getCustomer(string $customerAccount):array
   {

      try {
         
         $user = Auth::user();
         $paymentDTO = $this->momoDTO->fromArray([
                                                      'customerAccount' => $customerAccount,
                                                      'paymentAmount' => 100,
                                                      'client_id' => $user->client_id
                                                   ]);
         $paymentDTO = $this->enquiryHandler->handle($paymentDTO);

         return [
                     "customerAccount" => $customerAccount, 
                     "customerName" => $paymentDTO->customer['name'], 
                     "customerAddress" => $paymentDTO->customer['address'], 
                     "accountBalance" => $paymentDTO->customer['balance']
                  ];

      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

}