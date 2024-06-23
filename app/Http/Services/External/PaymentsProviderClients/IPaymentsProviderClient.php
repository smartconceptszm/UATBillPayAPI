<?php

namespace App\Http\Services\External\PaymentsProviderClients;

interface IPaymentsProviderClient 
{

      public function requestPayment(object $dto): object;

      public function confirmPayment(object $dto): object;

}