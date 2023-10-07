<?php

namespace App\Http\Services\USSD\Utility;

class StepService_AccountNoMenu
{
    public function handle(String $urlPrefix): String
    {

        $client = \strtoupper($urlPrefix);
        $accountExamples = \config('efectivo_clients.'.$urlPrefix.'.postpaidExample');
        $menuItem = "Enter ".$client." Account Number in full";
        $menuItem = $accountExamples? $menuItem." (for example: ".$accountExamples.")":$menuItem;
        $menuItem = $menuItem.":\n";
        return $menuItem;

    }

}

