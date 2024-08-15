<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebAppPaymentViaCardTest extends TestCase
{

   public function _test_pay_via_momo(): void
   {

      //Main Menu
      // '' => 'required|string',
      // '' => 'required|string',


      $wallet_id = 'd617baa6-7307-11ee-b8ce-fec6e52a2330';
      $menu_id = '8a2d7988-7306-11ee-b8ce-fec6e52a2330';
      $walletNumber ='1234123412341234';
      $customerAccount = 'KCT10482';
      $mobileNumber ='260977787659';
      $cardHolderName = 'John Doe';
      $paymentAmount = '10.50';
      $cardExpiry = '12/2024';
      $cardCVV = '123';


      $response = $this->post('/app/paymentsviamomo', 
                                    ['customerAccount' => $customerAccount,'paymentAmount' =>$paymentAmount,
                                    'cardHolderName' => $cardHolderName, 'walletNumber' => $walletNumber,
                                    'mobileNumber' =>$mobileNumber,'menu_id' => $menu_id,
                                    'wallet_id' =>$wallet_id, 'cardExpiry' =>$cardExpiry,
                                    'cardCVV' => $cardCVV
                                 ]);
      $response = $response->json()['data'];
      $this->assertTrue(\count($response)>0);

   }


}
