<?php

namespace Tests\Feature;


use Tests\TestCase;

class WebPaymentAnalyticsRegularTest extends TestCase
{

   public function _test_get_analytics_regular(): void
   {

      // $paymentToReviewService = new \App\Http\Services\Payments\PaymentToReviewService();
      $paymentDTO = new \App\Http\DTOs\MoMoDTO();

      // $thePayment = $paymentToReviewService->findById('9d5b9ed3-2491-47db-903b-d24cabb0c041');
      // $paymentDTO = $paymentDTO->fromArray(\get_object_vars($thePayment));

      $thePayment = json_decode('{"id":"9d683933-bba2-486c-adbe-66198577f0c2","exitPipeline":false,"validationRules":null,"payments_provider_id":"0fd6f718-730b-11ee-b8ce-fec6e52a2330","ppTransactionId":"6011360618","surchargeAmount":0,"customerAccount":"0120220021535","paymentAmount":20,"receiptAmount":20,"transactionId":"88b6a69b-0669-495e-80ed-e61960906b4c","receiptNumber":"1730709739WCJADB","paymentStatus":"RECEIPT DELIVERED","walletHandler":"MTN","mobileNumber":"260966624910","walletNumber":"260966624910","tokenNumber":"1246- 506-2 34-08 7-440 -0434","session_id":"9d6838f8-5dc4-4c38-b774-12a7f5e3999c","created_at":"2024-10-04 10:40:18","updated_at":null,"wallet_id":"9b84668f-18cc-48d6-ace2-66d06621f4aa","client_id":"39d62961-7303-11ee-b8ce-fec6e52a2330","reference":"260966624910","revenuepoint":"KITWE","receipt":"\nPayment successful\nAmount: ZMW 20.00\nMeter No: 0120220021535\nAcc: 0120220021535\nToken: 1246- 506-2 34-08 7-440 -0434\nDate: 04-Nov-2024\n","menu_id":"24aaacd7-d877-11ee-98ed-0a3595084709","user_id":null,"channel":"USSD","status":"REVIEWED","error":"","customerJourney":"2021*2*0120220021535*260966624910*20.00*1","clientSurcharge":"NO","testMSISDN":null,"sessionId":"17307095790312349","urlPrefix":"nkana","shortCode":"2021","customer":[],"mno_id":"0fd6f718-730b-11ee-b8ce-fec6e52a2330","sms":{"status":"DELIVERED","error":""}}');
      $thePayment = $paymentDTO->fromArray(\get_object_vars($thePayment));


      $regularAnalyticsService= new \App\Http\Services\Analytics\RegularAnalyticsService(
                                       new \App\Http\Services\Analytics\AnalyticsGeneratorService(),
                                       new \App\Http\Services\Clients\ClientWalletService(new \App\Models\ClientWallet())
                                    );

      $response = $regularAnalyticsService->generate( $paymentDTO);

      $this->assertTrue($response);


      // use Illuminate\Support\Carbon;
      // $theDate = Carbon::yesterday();
      
   }

}
