<?php

namespace Tests\Feature;

use Tests\TestCase;

class RegularAnalyticsGeneratorTest extends TestCase
{

   public function _test_analytics_generation(): void
   {
      $paymentDTO = new \App\Http\DTOs\MoMoDTO();
      $paymentDTO = $paymentDTO->fromArray(
         [
            'wallet_id' => '9dcb5dba-df36-4c09-bced-d2f0ddb4b3be',
            'created_at' => '2025-03-07',
         ]);
      $regularGenerator = new \App\Http\Services\Analytics\RegularAnalyticsService(
                              new \App\Http\Services\Clients\DashboardGeneratorsOfClientService(),
                              new \App\Http\Services\Clients\ClientWalletService(new \App\Models\ClientWallet())
                           );

      $response = $regularGenerator->generate($paymentDTO);
      
      $this->assertTrue($response == true);

   }

}
