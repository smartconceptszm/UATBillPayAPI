<?php

namespace App\Http\Services\MoMo\BillingClientCallers;

use App\Http\Services\Contracts\EfectivoPipelineWithBreakContract;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class PostPaymentMock extends EfectivoPipelineWithBreakContract
{
    private $newBalance;

    protected function stepProcess(BaseDTO $momoDTO)
    {
        if (\env('USE_RECEIPTING_MOCK') == "YES"){
            $momoDTO->stepProcessed=true;
            $this->newBalance="0";
            $momoDTO->receiptNumber = "RCPT".\rand(1000,100000);
            $momoDTO->paymentStatus = "RECEIPTED";
            $momoDTO->receipt = $this->formatReceipt($momoDTO->receiptNumber,
                                        $momoDTO->receiptAmount, $momoDTO->accountNumber);
        }
        return $momoDTO;

    }

    private function formatReceipt(string $receiptNumber, 
                $receiptAmount, string $accountNumber ): string
    {

        $receipt = "Payment successful\n" .
            "Rcpt No.: " . $receiptNumber . "\n" .
            "Amount: ZMW " . \number_format($receiptAmount, 2, '.', ',') . "\n".
            "Acc: " . $accountNumber."\n";
            if($this->newBalance!="0"){
                $receipt.="Bal: ZMW ".$this->newBalance . "\n";
            }
        $receipt.="Date: " . Carbon::now()->format('d-M-Y') . "\n";
        return $receipt;

    }
}