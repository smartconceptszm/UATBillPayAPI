<?php

namespace App\Http\Services\External\BillingClients;

use App\Http\Services\External\BillingClients\IBillingClient;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Jobs\SendSMSesJob;
use App\Http\DTOs\BaseDTO;

class EnquiryHandler
{

	public function __construct(
		private IBillingClient $billingClient)
	{}

	function handle(BaseDTO $txDTO): BaseDTO
	{

		try {
			$txDTO->customer = $this->billingClient->getAccountDetails([
																		'customerAccount'=>$txDTO->customerAccount,
																		'paymentAmount'=>$txDTO->paymentAmount,
																		'client_id'=>$txDTO->client_id
																	]);
			$txDTO->district = $txDTO->customer['district'];
			Cache::forget($txDTO->urlPrefix.'_BillingErrorCount');
		} catch (\Throwable $e) {
			if($e->getCode()==1 || $e->getCode()==4){
				throw new \Exception($e->getMessage(), $e->getCode());
			}else{
				$billingServiceErrorCount = (int)Cache::get($txDTO->urlPrefix.'_BillingErrorCount');
				if($billingServiceErrorCount){
					if (($billingServiceErrorCount+1) < (int)\env('BILLING_ERROR_THRESHOLD')) {
						Cache::increment($txDTO->urlPrefix.'_BillingErrorCount');
					}else{
						//Send Notification here
								
								$adminMobileNumbers = \explode("*",\env('APP_ADMIN_MSISDN'));
								if($txDTO->testMSISDN){
									$clientMobileNumbers = \explode("*",$txDTO->testMSISDN);
									$adminMobileNumbers=\array_merge($clientMobileNumbers,$adminMobileNumbers);
								}
								
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
													new SendSMSesJob($arrSMSes,$txDTO->client_id),'','low');
						//
						Cache::put($txDTO->urlPrefix.'_BillingErrorCount', 1, 
													Carbon::now()->addMinutes((int)env('BILLING_ERROR_CACHE')));
					}
				}else{
					Cache::put($txDTO->urlPrefix.'_BillingErrorCount', 1,
												Carbon::now()->addMinutes((int)env('BILLING_ERROR_CACHE')));
				}
				throw new \Exception($e->getMessage(), 2);
			}   
		}
      
		return $txDTO;
	}
    
}