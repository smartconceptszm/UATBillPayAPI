<?php

namespace Tests\Feature;

use Illuminate\Support\Carbon;
use Tests\TestCase;

class BillingPrePaidClientChambeshiTest extends TestCase
{

   public function _test_Get_Account(): void
   {

   }

   public function _test_ButToken(): void
   {

      //Main Menu
      $params = [
                  'total_paid' =>'1000',
                  "meter_number"=> '0166200084274',
                  'client_id' => '39d62802-7303-11ee-b8ce-fec6e52a2330',
                  'debt_percent'=> 50
               ];


      $billingClient =  new \App\Http\Services\External\BillingClients\ChambeshiPrePaid(
         new \App\Http\Services\Clients\BillingCredentialService( new \App\Models\BillingCredential()),
         new \App\Http\Services\External\BillingClients\ChambeshiPostPaid(new \App\Http\Services\Clients\BillingCredentialService( new \App\Models\BillingCredential()))
      );
      $response = $billingClient->getAccountDetails($params);
      $this->assertTrue($response);

   }

}
