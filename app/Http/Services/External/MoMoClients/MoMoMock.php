<?php

namespace App\Http\Services\External\MoMoClients;

use App\Http\Services\External\MoMoClients\IMoMoClient;
use Illuminate\Support\Str;

class MoMoMock implements IMoMoClient
{

   public function requestPayment(object $dto): object
   {
      return (object)[
               'status' => 'SUBMITTED',
               'transactionId' => (string)Str::uuid(),
               'error' => '',
         ];
   }

   public function confirmPayment(object $dto): object
   {
      return (object)[
            'status' => "PAID | NOT RECEIPTED",
            'mnoTransactionId' => 'MP'.date('ymd').".".date('Hi').".".strtoupper(Str::random(6)),
            'error' => '',
         ];
   }

}
