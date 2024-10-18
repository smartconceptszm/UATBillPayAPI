<?php

use App\Http\Services\SMS\SMSService;
use Tests\TestCase;

class SMSTest extends TestCase
{

    // /**
    //  * @runInSeparateProcess
    //  */

    public function _testSendSMS()
    {   

        $smsDTO = new \App\Http\DTOs\SMSTxDTO();
        $smsDTO = $smsDTO->fromArray([
                                        'client_id'=>''
                                    ]);

        $smsSender = new SMSService(
                        new \App\Http\Services\SMS\MessageService(new \App\Models\Message([])),
                        new \App\Http\Services\External\SMSClients\ZamtelSMS(),
                        new \App\Http\Services\Clients\ClientMnoService(new \App\Models\ClientMno([])),
                        new \App\Http\Services\Clients\MnoService(new \App\Models\MNO([])),
                        new \App\Http\Services\Clients\ClientService(new \App\Models\Client([])),
                        new \App\Http\DTOs\SMSTxDTO()
                    );
        $response = $smsSender->send($smsDTO);
        
        $this->assertTrue($response['smsStatus'] == 'DELIVERED');

    }

    public function _testMTNSMS()
    {   

        $smsClient = new \App\Http\Services\External\SMSClients\MTNMoMoDeliverySMS(
                                    new \App\Http\Services\Clients\ClientWalletCredentialsService(new \App\Models\ClientWalletCredential())
                                );
        $response = $smsClient->send([
                                        'transactionId'=>"fefaf6a4-fcff-43c7-8161-dd192513bdf7",
                                        'clientShortName'=>'kafubu',
                                        'mobileNumber'=>'260965199175',
                                        'urlPrefix'=>'kafubu',
                                        'wallet_id'=>'9c57560e-2836-425a-a848-ace6238e5845',
                                        'client_id'=>"cba1c60c-240f-11ef-b077-8db5e354f7db",
                                        'message'=>"Rcpt No: 7"."\n".
                                                    "Amount: ZMW 1.00"."\n".
                                                    "Acc: 1053280"."\n".
                                                    "Bal: ZMW 1,570.39"."\n".
                                                    "Date: 24-Jun-2024"
                                    ]);
        
        $this->assertTrue($response['smsStatus'] == 'DELIVERED');

    }

}