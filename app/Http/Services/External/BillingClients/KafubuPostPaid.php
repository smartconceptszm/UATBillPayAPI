<?php

namespace App\Http\Services\External\BillingClients;

use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Web\Clients\BillingCredentialService;
use App\Http\Services\Utility\XMLtoArrayParser;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Exception;

class KafubuPostPaid implements IBillingClient
{

    private $kafubuSoapService;
    private string $soapUserName;
    private string $soapPassword;
    private string $soapToken;
    private string $cashierNo;
    private string $operator;
    private $cacheTTL;

    public function __construct(
        private BillingCredentialService $billingCredentialsService,
        private XMLtoArrayParser $xmlToArrayParser)
    {}


    public function getAccountDetails(array $params): array
    {

        $response = [];

        try {

            $this->setConfigs($params['client_id']);

            $getDebtBalParams=[ 
                'functionName' => 'getdebtbal',
                'rdusername' => $this->soapUserName,
                'rdpassword' => $this->soapPassword,
                'token' => $this->soapToken ,
                'account' => $params['customerAccount'],
            ];

            $apiResponse = $this->kafubuSoapService->getdebtbal($getDebtBalParams);

            if(\substr($apiResponse->promunError,0,7)!="00 - OK"){
                Log::error(' Kafubu Billing Client error (getdebtbal for account number '.$params['customerAccount'].'): '.$apiResponse->promunError);
                if(substr($apiResponse->promunError,0,2)=="14"){
                    //throw new Exception("Customer account number not found",1);
                    throw new Exception("Invalid Kafubu POST-PAID Account Number",1);
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
            $cachedBalance = \json_decode(Cache::get('kafubu_balance_'.$params['customerAccount'],\json_encode([])), true);
            if($cachedBalance){
                if(\key_exists('newBalance',$cachedBalance)){
                    if($theBalance>$cachedBalance['newBalance']){
                        $theBalance=$cachedBalance['newBalance'];
                    }else{
                        Cache::forget('kafubu'.$params['customerAccount']);
                    }
                }
            }

            $getDebtStaticParams=[ 
                'functionName' => 'getdebstatic',
                'rdusername' => $this->soapUserName,
                'rdpassword' => $this->soapPassword,
                'token' => $this->soapToken ,
                'account' => $params['customerAccount'],
            ];

            $apiResponse = $this->kafubuSoapService->getdebstatic($getDebtStaticParams);

            if(\substr($apiResponse->promunError,0,7)!="00 - OK"){
                Log::error(' Kafubu Billing Client (getdebstatic  for account number '.$params['customerAccount'].'): '.$apiResponse->promunError);
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
                "customerAccount" => $customerBalance['account'],
                "name" => $theCustomer['name'],
                "address" => $fullAddress,
                "district" => $theCustomer['deptDesc'],
                "mobileNumber" => $theCustomer['cell'],
                "balance" => \number_format($theBalance, 2, '.', ','),
            ];

        } catch (\Throwable $e) {
            if ($e->getCode() == 1) {
                throw $e;
            } 
            if ($e->getCode() == 2) {
                throw $e;
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
            $this->setConfigs($postParams['client_id'],$postParams['providerName']);
            $receiptingParams=[ 
                'functionName' => 'createreceipt1',
                'rdusername' => $this->soapUserName,
                'rdpassword' => $this->soapPassword,
                'token' => $this->soapToken ,
                'recDate' => \date('Ymd'),
                'mcno' => $this->cashierNo,
                'operator' => $this->operator,
                'account' => $postParams['account'],
                'incomeCode' => 'ZZ',
                'payType' => 'B',
                'recTime' => '',
                'amount' => $postParams['amount'],
                'reference' => $postParams['reference']
            ];

            $apiResponse = $this->kafubuSoapService->createreceipt1($receiptingParams);
            if(substr($apiResponse->promunError,0,7)!="00 - OK"){
                Log::error(' Kafubu Billing Client (updatepreceipts) error: '.$apiResponse->promunError);
                throw new Exception(' Kafubu Billing Client (updatepreceipts) error: '.$apiResponse->promunError,1);
            }
            
            try {
                $theReceipt = $this->xmlToArrayParser->handle($apiResponse->promunResponse);
            } catch (\Throwable $e) {
                Log::error(' Kafubu Billing Client (updatepreceipts  for account number '.$postParams['customerAccount'].'): '.$apiResponse->promunError);
                throw new Exception(" Kafubu Billing Client (XML response extraction) error. Details: ".$e->getMessage(),1);
            }
            $response['status']="SUCCESS";
            $response['receiptNumber']=$theReceipt['recno'];

            //Account for uncreditted receipts
            $cacheValues=[];
            $cacheValues['newBalance']= $postParams['balance']-$postParams['amount'];

            $cacheTTL =intval($this->cacheTTL)*2;
            if(Carbon::now()->dayOfWeek == 5 || Carbon::now()->dayOfWeek == 6){
                $cacheTTL+=intval($this->cacheTTL);
            }

            Cache::put('kafubu_balance_'.$postParams['customerAccount'],\json_encode($cacheValues), 
                Carbon::now()->addMinutes($cacheTTL));

        } catch (\Throwable $e) {
            if ($e->getCode() == 1) {
                $response['error']=$e->getMessage();
            } else{
                $response['error']=" Kafubu Billing Client (Post Payment) error. Details: " . $e->getMessage();
            }
        }

        return $response;

    }

    public function changeCustomerNumber(
        String $customerAccount,
        String $newMobileNo,
        String $phoneNumber
    ): String {

        $response = 'KWSC'.$customerAccount.$newMobileNo;

        return $response;
    }

    private function setConfigs(string $client_id,string $providerName=null)
    {

        $clientCredentials = $this->billingCredentialsService->getClientCredentials($client_id);
        $baseURL = $clientCredentials['BASE_URL'];
        $wsdlPath = $baseURL.$clientCredentials['wsdl_URI'];
        $soapOptions =  [
                                'exceptions' => true,
                                'cache_wsdl' => WSDL_CACHE_BOTH,
                                'soap_version' => SOAP_1_1,
                                'trace' => 1,
                                'connection_timeout' => $clientCredentials['SOAP_CONNECTION_TIMEOUT']
                            ];
        $this->kafubuSoapService = new \SoapClient($wsdlPath,$soapOptions);
        $this->kafubuSoapService->__setLocation($baseURL);
        if($providerName){
            $this->cashierNo = $clientCredentials['SOAP_CASHIER_NO_'.$providerName];
        }
        $this->soapUserName =$clientCredentials['SOAP_USERNAME'];
        $this->soapPassword = $clientCredentials['SOAP_PASSWORD'];
        $this->operator = $clientCredentials['SOAP_OPERATOR'];
        $this->cacheTTL = $clientCredentials['BALANCE_CACHE'];
        $this->soapToken = $clientCredentials['SOAP_TOKEN'];

    }

}
