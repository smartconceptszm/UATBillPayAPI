<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebAppPaymentViaMoMoTest extends TestCase
{

   public function _test_pay_via_momo(): void
   {

      //Main Menu
      $wallet_id = 'd617baa6-7307-11ee-b8ce-fec6e52a2330';
      $client_id = '39d62802-7303-11ee-b8ce-fec6e52a2330';
      $menu_id = '8a2d7988-7306-11ee-b8ce-fec6e52a2330';
      $mobileNumber ='260977787659';
      $walletNumber ='260977787659';
      $customerAccount = 'KCT10482';
      $paymentAmount = '10.50';

      $response = $this->post('/app/paymentsviamomo', 
                                    ['customerAccount' => $customerAccount,'paymentAmount' =>$paymentAmount,
                                    'walletNumber' => $walletNumber,'mobileNumber' =>$mobileNumber,
                                    'menu_id' => $menu_id,'client_id' =>$client_id,
                                    'wallet_id' => $wallet_id
                                 ]);
      $response = $response->json()['data'];
      $this->assertTrue(\count($response)>0);

   }


}
