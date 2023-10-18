<?php

use App\Http\Services\Utility\SMSTheReceipt;
use App\Http\BillPayServices\SMS\Gateway\Step_DB_SAVE;
use Tests\TestCase;

class SMSTest extends TestCase
{

    // /**
    //  * @runInSeparateProcess
    //  */

    public function _testSendSMS()
    {   

        $txDTO=[
            'input' => [
                "mnoName"=>'AIRTEL',
                'subscriberInput'=>'1',
                'isnewrequest' => '0',
                'sessionId' => '10000022',
                'mobileNumber'=>'+260972702707'
            ],
            'recordId' => '79',
            'customerJourney' => '2106*1*1116000737*8',
            'updatedCustomerJourney' => '2106*1*1116000737*8*1',
            'customer' => [
                'accountNumber' => '1116000737',
                'name' => 'Kabwe Kabwe',
                'address' => 'Kabwe',
                'district'=>'BNC',
                'mobileNumber' => '0',
                'balance' => '20',
            ],
            'transaction' => [
                'id' => '2302151051291116000737B7J',
                'amount' => 7.00,
                'mnoTransactionId' => '',
                'status' => 'SUBMITTED',
                'type' => 1,
                'referenceNumber' => '',
                'receiptNumber' => '',
                'receipt' => '',
                'fireMoMoRequest'=>true,
                'formatReceiptParams'=>[],
            ],

            'mno' => [
                'id'=>'1',
                'name' => 'AIRTEL',
                'account' => '+260972702707',
                'response'=>[],
                'testMSISDN' => [],
                'logAll'=>'NO'
            ],

            'client'=>[
                'id'=>'2', 
                'code' => 'LgWSSC',
                'urlPrefix' => 'lukanga',
                'shortName' => 'LUKANGA',
                'smsCharge' => '0.1',
                'balance' => '99',
                'type' => 'POST-PAID',
                'mode' => 'UP',
                'ussdCode'=>'2106', 
                'status'=>'ACTIVE'
            ],

            'sms'=>[
                'send'=>false,
                'mobileNumber'=>'',
                'message'=>'',
                'amount'=>'',
                'type'=>'DEBIT',
                'status'=>'INITIATED',
                'userId'=>null,
                'error'=>''
            ],

            'menuNo' => '1',
            'step' => '4',
            'subMenu' => '',
            'level' => [
                "2106",
                "1",
                "1116000737",
                "7"
            ],

            'response' => '',
            'lastResponse' => true,
            'status' => 'INITIATED',
    
            'cleaned' => 'NO',
            'error' => '',
            'created_at' => ''
        ];

        $smsSender = new SMSTheReceipt();
        $response = $smsSender->send($txDTO);
        
        $this->assertTrue($response['smsStatus'] == 'DELIVERED');

    }


    public function _testSaveSMS(): void
    {

        $smsParams=[];
        $smsParams['user_id']='';
        $smsParams['mobileNumber']= '+260974282737';
        $smsParams['message']="Payment successful"."\n".
                                "Rcpt No.: 31"."\n".
                                "Amount: ZMW 46.00"."\n".
                                "Acc: 1105000368"."\n".
                                "Bal: ZMW 55.23"."\n".
                                "Date: 22-Apr-2023";
        $smsParams['mno_id']=1;
        $smsParams['client_id']=2;
        $smsParams['amount']=0.1;
        $smsParams['type']='RECEIPT';
        $smsParams['status']='DELIVERED';
        $smsParams['clientShortName']='LUKANGA';
        $smsParams['smsPayMode']='POST-PAID';
        $smsParams['clientBalance']=999;
        $smsParams['resending']=false;
        $smsParams['user_id']=$smsParams['user_id']==''?null:$smsParams['user_id'];
        $smsParams['error']='';
        $smsParams['send']=false;
        $smsParams['id']='';

        $saver = new Step_DB_SAVE();
        $response = $saver->saving($smsParams);
        $this->assertTrue($response['smsStatus'] == 'DELIVERED');

    }

}