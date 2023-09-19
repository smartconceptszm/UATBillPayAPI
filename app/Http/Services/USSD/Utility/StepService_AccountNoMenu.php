<?php

namespace App\Http\Services\USSD\Utility;

class StepService_AccountNoMenu
{
    public function handle(String $prePaidText, String $urlPrefix): String
    {

        $client = \strtoupper($urlPrefix);
        $accountExamples = $prePaidText? \config('efectivo_clients.'.$urlPrefix.'.prepaidExample'):
                        \config('efectivo_clients.'.$urlPrefix.'.postpaidExample');
        $menuItem = $prePaidText == "" ? "Enter ":"Enter ".$prePaidText." ";
        $menuItem = $menuItem.$client." Account Number in full";
        $menuItem = $accountExamples? $menuItem." (for example: ".$accountExamples.")":$menuItem;
        $menuItem = $menuItem.":\n";
        return $menuItem;

    }

}

