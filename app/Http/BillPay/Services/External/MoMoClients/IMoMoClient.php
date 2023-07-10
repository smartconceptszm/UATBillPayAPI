<?php

namespace App\Http\BillPay\Services\External\MoMoClients;

interface IMoMoClient 
{

      public function requestPayment(object $dto): object;

      public function confirmPayment(object $dto): object;

}