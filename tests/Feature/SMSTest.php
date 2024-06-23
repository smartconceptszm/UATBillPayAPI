<?php

use App\Http\Services\Web\SMS\SMSService;
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
                        new \App\Http\Services\Web\SMS\MessageService(new \App\Models\Message([])),
                        new \App\Http\Services\External\SMSClients\ZamtelSMS(),
                        new \App\Http\Services\Web\Clients\ClientMnoService(new \App\Models\ClientMno([])),
                        new \App\Http\Services\Web\Clients\MnoService(new \App\Models\MNO([])),
                        new \App\Http\Services\Web\Clients\ClientService(new \App\Models\Client([])),
                        new \App\Http\DTOs\SMSTxDTO()
                    );
        $response = $smsSender->send($smsDTO);
        
        $this->assertTrue($response['smsStatus'] == 'DELIVERED');

    }

}