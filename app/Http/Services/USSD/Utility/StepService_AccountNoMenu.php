<?php

namespace App\Http\Services\USSD\Utility;

class StepService_AccountNoMenu
{

    public function handle(String $urlPrefix, String $accountType): String
    {
        $client = \strtoupper($urlPrefix);
        if($accountType == 'POST-PAID'){
            $accountExamples = \env(\strtoupper($urlPrefix).'_POSTPAID_EXAMPLE');
            $menuItem = "Enter ".$client." Account Number in full";
        }else{
            $accountExamples = \env(\strtoupper($urlPrefix).'_PREPAID_EXAMPLE');
            $menuItem = "Enter ".$client." Meter Number in full";
        }
        $menuItem = $accountExamples? $menuItem." (for example: ".$accountExamples.")":$menuItem;
        $menuItem = $menuItem.":\n";
        return $menuItem;
    }

}

