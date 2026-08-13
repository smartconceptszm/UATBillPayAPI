<?php

namespace Tests\Feature;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class BillingClientLukangaPrepaidTest extends TestCase
{

   public function _test_Get_Account(): void
   {

      $params = [
               'paymentAmount' =>'1000',
               "customerAccount"=> '0120210630998',
               'client_id' => '39d62460-7303-11ee-b8ce-fec6e52a2330',
               'debt_percent'=> 50
            ];


      $billingClient =  new \App\Http\Services\External\BillingClients\LukangaPrePaid(
                           new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential()),
                           new \App\Http\Services\External\BillingClients\PrePaidVendor\PurchaseEncryptor()
                        );

      $response = $billingClient->getAccountDetails($params);

      $this->assertTrue($response);

   }

   public function _test_BuyToken(): void
   {

      $receiptNumber = \now()->timestamp.\strtoupper(Str::random(6));
      $params = [
               'paymentAmount' =>'1000',
               "customerAccount"=> '0120210630998',
               "transactionId" => $receiptNumber,
               'client_id' => '39d62460-7303-11ee-b8ce-fec6e52a2330',
               'debt_percent'=> 50
            ];


      $billingClient =  new \App\Http\Services\External\BillingClients\LukangaPrePaid(
                           new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential()),
                           new \App\Http\Services\External\BillingClients\PrePaidVendor\PurchaseEncryptor()
                        );

      $response = $billingClient->generateToken($params);

      $this->assertTrue($response);

   }

}
