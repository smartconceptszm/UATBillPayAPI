<?php

namespace App\Http\Services\External\PaymentsProviderClients;

use App\Http\Services\External\PaymentsProviderClients\IPaymentsProviderClient;
use Illuminate\Support\Str;

class MockWallet implements IPaymentsProviderClient
{

   public function requestPayment(object $dto): object
   {
      
      return (object)[
               'status' => 'SUBMITTED',
               'transactionId' => (string)Str::uuid(),
               'error' => '',
         ];

      // return (object)[
      //          'status' => 'SUBMISSION FAILED',
      //          'transactionId' => (string)Str::uuid(),
      //          'error' => 'Error on collect funds.',
      //    ];

   }

   public function confirmPayment(object $dto): object
   {

      return (object)[
            'status' => "PAID | NOT RECEIPTED",
            'ppTransactionId' => 'MP'.date('ymd').".".date('Hi').".".strtoupper(Str::random(6)),
            'error' => '',
         ];

      // return (object)[
      //       'status' => "PAYMENT FAILED",
      //       'ppTransactionId' => '',
      //       'error' => 'Error on get transaction status.',
      //    ];
   }

}
