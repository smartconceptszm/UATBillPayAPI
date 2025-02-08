<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\DashboardPaymentStatusTotals;
use Tests\TestCase;

class UtilityTest extends TestCase
{

   /**
    * A basic test example.
    */
   public function _test_new_code(): void
   {


      $response =  '{"Response":{"ReFNo":"9"}}';
      $response = json_decode($response,true);
      $response = $response['Response']['ReFNo'];

      
      // $tokenArr = \explode(",", '32616529891228143314,32616529891228143314,32616529891228143314,32616529891228143314');
      // $formattedTokens = [];
      // foreach ($tokenArr as $value) {
      //    $formattedTokens[]=\implode('-', \str_split(str_replace(' ', '', $value), 4));
      // }
      // $response = \implode(',',$formattedTokens);

      $this->assertTrue($response);

   }

   /**
    * A basic test example.
    */
   public function _test_a_feature(): void
   {


      $currentEntries = DashboardPaymentStatusTotals::where([
                                          ['dateOfTransaction', '=', '2024-10-24'],
                                          ['client_id', '=', '39d6269a-7303-11ee-b8ce-fec6e52a2330'],
                                       ])
                                       ->pluck('id')
                                       ->toArray();

      DashboardPaymentStatusTotals::destroy($currentEntries);

      $currentEntries = DashboardPaymentStatusTotals::where([
                                                               ['dateOfTransaction', '=', '2024-10-24'],
                                                               ['client_id', '=', '39d6269a-7303-11ee-b8ce-fec6e52a2330'],
                                                            ])
                                                            ->pluck('id')
                                                            ->toArray();

      $this->assertTrue($currentEntries);

   }

}
