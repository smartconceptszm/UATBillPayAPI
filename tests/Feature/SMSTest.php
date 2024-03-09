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

        $smsDTO= new \App\Http\DTOs\SMSTxDTO();
        $smsDTO= $smsDTO->fromArray([

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

}