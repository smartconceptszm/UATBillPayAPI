<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MoMoCallbackTest extends TestCase
{

   public function _test_airtel_callback(): void
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
