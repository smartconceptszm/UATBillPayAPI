<?php

namespace App\Http\Services\USSD\Utility;

use App\Http\Services\External\BillingClients\IBillingClient;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Jobs\SendSMSesJob;
use Exception;

class StepService_GetCustomerAccount 
{

    public function __construct(
        private IBillingClient $billingClient)
    {}

    public function handle(string $accountNumber, string $urlPrefix , string $client_id):array
    {

        try {
            $arrAccount=[];
            if (\env('USE_BILLING_MOCK')=="YES"){
                return [
                            "accountNumber" => $accountNumber,
                            "name" => \strtoupper($urlPrefix)." Customer",
                            "address" => "No. 1, Street 1, ".\strtoupper($urlPrefix),
                            "district" => \strtoupper($urlPrefix),
                            "mobileNumber" => "260761028631",
                            "balance" => \number_format(100, 2, '.', ','),
                    ]; 
            }

            $customer = Cache::get($urlPrefix.$accountNumber);
            if($customer){
                return \json_decode($customer,true);
            }
            $arrAccount= $this->billingClient->getAccountDetails($accountNumber);
            Cache::put($urlPrefix.$accountNumber, 
                    \json_encode($arrAccount), 
                    Carbon::now()->addMinutes(intval(\env('CUSTOMER_ACCOUNT_CACHE'))));
            Cache::forget($urlPrefix.'_BillingErrorCount');

        } catch (Exception $e) {
            if($e->getCode()==1){
                throw new Exception("Customer account not found", 1);
            }else{
                $billingServiceErrorCount = (int)Cache::get($urlPrefix.'_BillingErrorCount');
                if($billingServiceErrorCount){
                    if (($billingServiceErrorCount+1) < (int)\env('BILLING_Error_THRESHOLD')) {
                        Cache::increment($urlPrefix.'_BillingErrorCount');
                    }else{
                        //Send Notification here
                            $clientMobileNumbers = \explode("*",\env(\strtoupper($urlPrefix).'_APP_TEST_MSISDN'));
                            $adminMobileNumbers = \explode("*",\env('APP_ADMIN_MSISDN'));
                            $adminMobileNumbers=\array_merge($adminMobileNumbers,$clientMobileNumbers);
                            $arrSMSes=[];
                            foreach ($adminMobileNumbers as $key => $mobileNumber) {
                                $arrSMSes[$key]['mobileNumber']=$mobileNumber;
                                $arrSMSes[$key]['type']="NOTIFICATION";
                                $arrSMSes[$key]['client_id']=$client_id;
                                $arrSMSes[$key]['message']=\strtoupper($urlPrefix).
                                        " billing system is currently offline - please check the service.";
                            }
                            Queue::later(Carbon::now()->addSeconds(1), 
                                            new SendSMSesJob($arrSMSes));
                        //
                        Cache::put($urlPrefix.'_BillingErrorCount', 1, 
                                            Carbon::now()->addMinutes((int)env('BILLING_Error_CACHE')));
                    }
                }else{
                    Cache::put($urlPrefix.'_BillingErrorCount', 1,
                                        Carbon::now()->addMinutes((int)env('BILLING_Error_CACHE')));
                }
                throw new Exception($e->getMessage(), 2);
            }   
        }
        return $arrAccount;
        
    }
    
}