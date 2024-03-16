<?php

namespace App\Http\Services\External\BillingClients;

use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Utility\XMLtoArrayParser;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Exception;

class LukangaPostPaid implements IBillingClient
{

    private XMLtoArrayParser $xmlToArrayParser;
    private $lukangaSoapService;
    private string $soapUserName;
    private string $soapPassword;
    private string $soapToken;
    private string $operator;

    public function __construct()
    {}

    public function getAccountDetails(string $accountNumber): array
    {

        $response = [];

        try {

            $this->setConfigs();

            if(!(\strlen($accountNumber)==10 || \strlen($accountNumber)==11)){
                throw new Exception("Invalid Account Number",1);
            }

            $getDebtBalParams=[ 
                'functionName' => 'getdebtbal',
                'rdusername' => $this->soapUserName,
                'rdpassword' => $this->soapPassword,
                'token' => $this->soapToken ,
                'account' => $accountNumber,
            ];

            $apiResponse = $this->lukangaSoapService->getdebtbal($getDebtBalParams);

            if(\substr($apiResponse->promunError,0,7)!="00 - OK"){
                Log::error(' Lukanga Billing Client error (getdebtbal for account number '.$accountNumber.'): '.$apiResponse->promunError);
                if(substr($apiResponse->promunError,0,2)=="14"){
                    throw new Exception("Customer account number not found",1);
                }else{
                    throw new Exception("Error getting balance from Promun. Details: ".$apiResponse->promunError,2);
                }
                
            }

            try {
                $customerBalance = $this->xmlToArrayParser->handle($apiResponse->promunResponse);
            } catch (\Throwable $e) {
                throw new Exception("Error extracting (getdebtbal) XML response. Details: ".$e->getMessage(),2);
            }

            $theBalance=0;
            if($customerBalance['items']){
                foreach ($customerBalance['items']['item'] as $billItem) {
                    if(\is_array($billItem)){
                        $theBalance+=(float)$billItem['bmf-tot'];
                    }else{
                        $theBalance+=(float)$customerBalance['items']['item']['bmf-tot'];
                        break;
                    }
                }
            }


            //Account for un creditted receipts
            $cachedBalance = \json_decode(Cache::get('lukanga_balance_'.$accountNumber,\json_encode([])), true);
            if($cachedBalance){
                if(\key_exists('newBalance',$cachedBalance)){
                    if($theBalance>$cachedBalance['newBalance']){
                        $theBalance=$cachedBalance['newBalance'];
                    }else{
                        Cache::forget('lukanga'.$accountNumber);
                    }
                }
            }

            $getDebtStaticParams=[ 
                'functionName' => 'getdebstatic',
                'rdusername' => $this->soapUserName,
                'rdpassword' => $this->soapPassword,
                'token' => $this->soapToken ,
                'account' => $accountNumber,
            ];

            $apiResponse = $this->lukangaSoapService->getdebstatic($getDebtStaticParams);

            if(\substr($apiResponse->promunError,0,7)!="00 - OK"){
                Log::error(' Lukanga Billing Client (getdebstatic  for account number '.$accountNumber.'): '.$apiResponse->promunError);
                throw new Exception("Error getting customer details from Promun. Details: ".$apiResponse->promunError,2);
            }

            try {
                $theCustomer = $this->xmlToArrayParser->handle($apiResponse->promunResponse);
            } catch (\Throwable $e) {
                throw new Exception("Error extracting (getdebstatic) XML response. Details: ".$e->getMessage(),2);
            }

            $fullAddress="";
            $fullAddress = !(\is_array($theCustomer['streetNo'])) ? $theCustomer['streetNo'] : $fullAddress ;
            $fullAddress = !(\is_array($theCustomer['streetName'])) ? $fullAddress. " ". $theCustomer['streetName'] : $fullAddress ;
            $fullAddress .= " ".$theCustomer['deptDesc'];

            $response=[
                "accountNumber" => $customerBalance['account'],
                "name" => $theCustomer['name'],
                "address" => $fullAddress,
                "district" => $theCustomer['deptDesc'],
                "mobileNumber" => $theCustomer['cell'],
                "balance" => \number_format($theBalance, 2, '.', ','),
            ];

        } catch (\Throwable $e) {
            if ($e->getCode() == 1) {
                throw new Exception($e->getMessage(), 1);
            } 
            if ($e->getCode() == 2) {
                throw new Exception($e->getMessage(), 2);
            } else{
                throw new Exception("Error executing 'Get Account Details' Soap Request. Details: " . $e->getMessage(), 2);
            }
        }
        return $response;

    }

    public function postPayment(Array $postParams): array 
    {

        $response=[
                'status'=>'FAILED',
                'receiptNumber'=>'',
                'error'=>''
            ];

        try {
            $this->setConfigs();
            $cashierNo = \env('LUKANGA_'.'SOAP_CASHIER_NO_'.$postParams['mnoName']);
            
            $receiptingParams=[ 
                'functionName' => 'updatepreceipts',
                'rdusername' => $this->soapUserName,
                'rdpassword' => $this->soapPassword,
                'token' => $this->soapToken ,
                'receiptDate' => \date('Ymd'),
                'cashierNo' => $cashierNo,
                'operator' => $this->operator,
                'account' => $postParams['account'],
                'reference' => $postParams['reference'],
                'incomeCode' => 'zz',
                'paytype' => 'C',
                'recTime' => '',
                'amount' => $postParams['amount'],
            ];

            $apiResponse = $this->lukangaSoapService->updatepreceipts($receiptingParams);
            if(substr($apiResponse->promunError,0,7)!="00 - OK"){
                throw new Exception(' Lukanga Billing Client (updatepreceipts) error: '.$apiResponse->promunError,1);
            }
            
            try {
                $theReceipt = $this->xmlToArrayParser->handle($apiResponse->promunResponse);
            } catch (\Throwable $e) {
                Log::error(' Lukanga Billing Client (updatepreceipts  for account number '.$postParams['account'].'): '.$apiResponse->promunError);
                throw new Exception(" Lukanga Billing Client (XML response extraction) error. Details: ".$e->getMessage(),1);
            }
            $response['status']="SUCCESS";
            $response['receiptNumber']=$theReceipt['recno'];

            //Account for uncreditted receipts
            $cacheValues=[];
            $cacheValues['newBalance']= $postParams['balance']-$postParams['amount'];

            $cacheTTL =intval(\env('LUKANGA_BALANCE_CACHE'))*2;
            if(Carbon::now()->dayOfWeek == 5 || Carbon::now()->dayOfWeek == 6){
                $cacheTTL+=intval(\env('LUKANGA_BALANCE_CACHE'));
            }
            Cache::put('lukanga_balance_'.$postParams['account'],\json_encode($cacheValues), 
                Carbon::now()->addMinutes($cacheTTL));

        } catch (\Throwable $e) {
            if ($e->getCode() == 1) {
                $response['error']=$e->getMessage();
            } else{
                $response['error']=" Lukanga Billing Client (Post Payment) error. Details: " . $e->getMessage();
            }
        }

        return $response;

    }

    public function changeCustomerNumber(
        String $accountNumber,
        String $newMobileNo,
        String $phoneNumber
    ): String {

        $response = 'LgWSSC'.$accountNumber.$newMobileNo;

        return $response;
    }


    private function setConfigs()
    {
       
        $wsdlPath = \env("LUKANGA_BASE_URL").\env("LUKANGA_wsdl_URI");
        $soapOptions =  [
                                'exceptions' => true,
                                'cache_wsdl' => WSDL_CACHE_BOTH,
                                'soap_version' => SOAP_1_1,
                                'trace' => 1,
                                'connection_timeout' => \env('LUKANGA_SOAP_CONNECTION_TIMEOUT')
                            ];
        $this->lukangaSoapService = new \SoapClient($wsdlPath,$soapOptions);
        $this->lukangaSoapService->__setLocation(\env("LUKANGA_BASE_URL"));

        $this->soapUserName = \env('LUKANGA_SOAP_USERNAME');
        $this->soapPassword = \env('LUKANGA_SOAP_PASSWORD');
        $this->soapToken = \env('LUKANGA_SOAP_TOKEN');
        $this->operator = \env('LUKANGA_SOAP_OPERATOR');
        $this->xmlToArrayParser = new XMLtoArrayParser();
        
    }


}
