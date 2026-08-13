<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MoMoMTNTest extends TestCase
{

   public function _test_Request(): void
   {


   }

   public function _test_Confirm(): void
   {

      //Main Menu
      $params = [
                  'customerAccount' => 'MBA11026',
                  'transactionId' => 'dcc55b64-96a0-4427-a39c-a99b9a0b3fc6',
                  'paymentAmount' => '15.00',
                  'mobileNumber' => '260967962059',
                  'walletNumber' => '260967962059',
                  'wallet_id' => 'd617bc18-7307-11ee-b8ce-fec6e52a2330'
               ];
         
      $mtnClient = new \App\Http\Services\External\PaymentsProviderClients\MTNMoMo(
         new \App\Http\Services\Clients\PaymentsProviderCredentialService (new \App\Models\PaymentsProviderCredential()),
         new \App\Http\Services\Clients\ClientWalletCredentialsService(new \App\Models\ClientWalletCredential()),
         new \App\Http\Services\Clients\ClientWalletService(new \App\Models\ClientWallet())
      );

      $response = $mtnClient->confirmPayment((object)$params);

      $this->assertTrue(\count($response)>0);

   }


}
