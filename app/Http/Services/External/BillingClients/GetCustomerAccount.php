<?php

namespace App\Http\Services\External\BillingClients;

use App\Http\Services\External\BillingClients\IBillingClient;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Jobs\SendSMSesJob;
use App\Http\DTOs\BaseDTO;
use Exception;

class GetCustomerAccount 
{

	public function __construct(
		private IBillingClient $billingClient)
	{}

	public function handle(BaseDTO $txDTO):BaseDTO
	{

		try {
			$txDTO->customer = \json_decode(Cache::get($txDTO->urlPrefix.
                                $txDTO->accountNumber,\json_encode([])), true);
			if(!$txDTO->customer){
				$txDTO->customer = $this->billingClient->getAccountDetails($txDTO->accountNumber);
				$txDTO->meterNumber = $txDTO->accountNumber;
				$txDTO->accountNumber = $txDTO->customer['accountNumber'];
				$txDTO->district = $txDTO->customer['district'];
				Cache::put($txDTO->urlPrefix.$txDTO->accountNumber, 
							\json_encode($txDTO->customer), 
							Carbon::now()->addMinutes(intval(\env('CUSTOMER_ACCOUNT_CACHE'))));
				Cache::forget($txDTO->urlPrefix.'_BillingErrorCount');
			}
		} catch (\Throwable $e) {

			if($e->getCode()==1 || $e->getCode()==4){
				throw new Exception($e->getMessage(), $e->getCode());
			}else{
				$billingServiceErrorCount = (int)Cache::get($txDTO->urlPrefix.'_BillingErrorCount');
				if($billingServiceErrorCount){
					if (($billingServiceErrorCount+1) < (int)\env('BILLING_ERROR_THRESHOLD')) {
						Cache::increment($txDTO->urlPrefix.'_BillingErrorCount');
					}else{
						//Send Notification here
								$clientMobileNumbers = \explode("*",$txDTO->testMSISDN);
								$adminMobileNumbers = \explode("*",\env('APP_ADMIN_MSISDN'));
								$adminMobileNumbers=\array_merge($adminMobileNumbers,$clientMobileNumbers);
								$arrSMSes=[];
								foreach ($adminMobileNumbers as $key => $mobileNumber) {
									$arrSMSes[$key]['message']=\strtoupper($txDTO->urlPrefix).
												" billing system is currently offline - please check the service.";
									$arrSMSes[$key]['urlPrefix']=$txDTO->urlPrefix;
									$arrSMSes[$key]['mobileNumber']=$mobileNumber;
									$arrSMSes[$key]['client_id']=$txDTO->client_id;
									$arrSMSes[$key]['type']="NOTIFICATION";
								}
								Queue::later(Carbon::now()->addSeconds(1), 
													new SendSMSesJob($arrSMSes));
						//
						Cache::put($txDTO->urlPrefix.'_BillingErrorCount', 1, 
													Carbon::now()->addMinutes((int)env('BILLING_ERROR_CACHE')));
					}
				}else{
					Cache::put($txDTO->urlPrefix.'_BillingErrorCount', 1,
												Carbon::now()->addMinutes((int)env('BILLING_ERROR_CACHE')));
				}
				throw new Exception($e->getMessage(), 2);
			}   
		}
		return $txDTO;
		
	}
    
}