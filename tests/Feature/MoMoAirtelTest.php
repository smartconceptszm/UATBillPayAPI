<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MoMoAirtelTest extends TestCase
{

   public function _test_Request(): void
   {


   }

   public function _test_Confirm(): void
   {

      //Main Menu
      $params = [
                  'customerAccount' => '0166209973',
                  'transactionId' => 'A0166209973D241225T155614',
                  'paymentAmount' => '200.00',
                  'mobileNumber' => '260977236582',
                  'walletNumber' => '260977236582',
                  'wallet_id' => 'd617baa6-7307-11ee-b8ce-fec6e52a2330'
               ];
         
      $airtelClient = new \App\Http\Services\External\PaymentsProviderClients\AirtelMoney(
                                 new \App\Http\Services\Clients\PaymentsProviderCredentialService(new \App\Models\PaymentsProviderCredential()),
                                 new \App\Http\Services\Clients\ClientWalletCredentialsService(new \App\Models\ClientWalletCredential()),
                                 new \App\Http\Services\Clients\ClientWalletService(new \App\Models\ClientWallet())
                              );

      $response = $airtelClient->confirmPayment((object)$params);

      $this->assertTrue(\count($response)>0);

   }


}
