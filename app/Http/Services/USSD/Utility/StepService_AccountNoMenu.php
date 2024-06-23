<?php

namespace App\Http\Services\USSD\Utility;

use App\Http\Services\Web\Clients\BillingCredentialService;
use App\Http\DTOs\BaseDTO;

class StepService_AccountNoMenu
{

    public function __construct( 
        private BillingCredentialService $billingCredentialService
    ){}

    public function handle(BaseDTO $txDTO): String
    {
        $billingCredentials = $this->billingCredentialService->getClientCredentials($txDTO->client_id);
        $client = \strtoupper($txDTO->urlPrefix);
        if($txDTO->accountType == 'POST-PAID'){
            $accountExamples = $billingCredentials['POSTPAID_EXAMPLE'];
            $menuItem = "Enter ".$client." Account Number in full";
        }else{
            $accountExamples = $billingCredentials['PREPAID_EXAMPLE'];
            $menuItem = "Enter ".$client." Pre-Paid Meter Number in full";
        }
        $menuItem = $accountExamples? $menuItem." (for example: ".$accountExamples.")":$menuItem;
        $menuItem = $menuItem.":\n";
        return $menuItem;
    }

}

