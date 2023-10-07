<?php

namespace App\Jobs;

use App\Http\Services\MoMo\InitiateMoMoPayment;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;
use App\Jobs\BaseJob;

class InitiateMoMoPaymentJob extends BaseJob
{

   private $momoDTO;
   public function __construct(BaseDTO $momoDTO)
   {
      $this->momoDTO = $momoDTO;
   }

   public function handle(InitiateMoMoPayment $initiateMoMoPayment)
   {
      
      //Bind the MoMoClient
      $momoClient = $this->momoDTO->mnoName;
      if(\env("MOBILEMONEY_USE_MOCK") == 'YES'){
         $momoClient = 'MoMoMock';
      }
      App::bind(\App\Http\Services\External\MoMoClients\IMoMoClient::class,$momoClient);
      //Handle the Job
      $initiateMoMoPayment->handle($this->momoDTO);

   }

}