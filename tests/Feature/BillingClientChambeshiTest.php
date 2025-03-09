<?php

namespace Tests\Feature;

use Illuminate\Support\Carbon;
use Tests\TestCase;

class BillingClientChambeshiTest extends TestCase
{

   public function _test_Get_Account(): void
   {

      //Main Menu
      $params = [
                  'customerAccount' => 'KCT10482',
                  'client_id' =>'39d62802-7303-11ee-b8ce-fec6e52a2330'
               ];

      $billingClient =  new \App\Http\Services\External\BillingClients\ChambeshiPostPaid(
                                 new \App\Http\Services\External\BillingClients\Chambeshi\Chambeshi(
                                             new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential()))
                              );

      $response = $billingClient->getAccountDetails($params);
      $this->assertTrue($response);

   }

   public function _test_PostPayment(): void
   {

      //Main Menu
      $params = [
                  'client_id' =>'39d62802-7303-11ee-b8ce-fec6e52a2330',
                  "txnDate"=> Carbon::now()->format('Y-m-d'),
                  'payment_provider' => 'airtel_money',
                  'payer_msisdn'=> '260977787659',
                  'account' => 'KCT10482',
                  'amount' => '2.00',
                  'txnId'=>"FJB2108157529348UAT",
                  'ReceiptNo'=>"KCT10482_".\now()->timestamp
               ];

      $billingClient =  new \App\Http\Services\External\BillingClients\ChambeshiPostPaid(
                  new \App\Http\Services\External\BillingClients\Chambeshi\Chambeshi(
                              new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential()))
               );
      $response = $billingClient->postPayment($params);
      $this->assertTrue($response);

   }

}
