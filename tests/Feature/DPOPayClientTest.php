<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DPOPayClientTest extends TestCase
{

   public function _test_Request(): void
   {

      //Main Menu
      $dpoClient = new \App\Http\Services\External\PaymentsProviderClients\DPOPay(
                           new \App\Http\Services\Clients\PaymentsProviderCredentialService(new \App\Models\PaymentsProviderCredential()),
                           new \App\Http\Services\Clients\ClientWalletCredentialsService(new \App\Models\ClientWalletCredential()),
                           new \App\Http\Services\Clients\ClientWalletService(new \App\Models\ClientWallet()));

      // $dto = (object)[
      //                      'wallet_id'=>'9d227ad4-21ff-4ffb-b856-7255cb9e726b',
      //                      'paymentAmount' => '6.00',
      //                      'creditCardNumber' => '4768171027093833',
      //                      'cardExpiry' => '29-08',
      //                      'cardCVV' => '019',
      //                      'cardHolderName' => 'Kelvin Malupande'
      //                   ];

         $dto = (object)[
                           'wallet_id'=>'9d227ad4-21ff-4ffb-b856-7255cb9e726b',
                           'paymentAmount' => '6.00',
                           'creditCardNumber' => '4383751000633245',
                           'cardExpiry' => '28-12',
                           'cardCVV' => '076',
                           'cardHolderName' => 'Brown Kasaro'
                        ];


      $response = $dpoClient->requestPayment($dto);
      $this->assertTrue(\count($response)>0);

   }

   public function _test_Confirm(): void
   {

      //Main Menu
      $status_code = 'TS';
      $code = 'DP00800001001';
      $airtel_money_id = 'MP240928.2123.H05897';
      $id ='ALIV0017400D240913T035853';
      $message ='Payment of ZMW 100.00 Till Number CHWSSC CHAMBESHI WATER SUPPLY CO. Airtel Money bal is ZMW 892.25. TID : MP240928.2123.H05897."},"\/airtelmoney\/callback';

      $response = $this->post('/airtelmoney/callback', 
                                 ['transaction' => 
                                    [
                                       'status_code' =>$status_code,
                                       'code' => $code,
                                       'airtel_money_id' =>$airtel_money_id,
                                       'id' => $id,
                                       'message' =>$message
                                    ]
                                 ]);
      $response = $response->json()['data'];
      $this->assertTrue(\count($response)>0);

   }


}
