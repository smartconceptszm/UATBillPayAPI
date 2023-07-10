<?php

namespace App\Http\BillPay\Services\MoMo\BillingClientCallers;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\External\BillingClients\IBillingClient;
use Illuminate\Support\Facades\Cache;
use App\Http\BillPay\DTOs\BaseDTO;
use Illuminate\Support\Carbon;

class PostPaymentLukanga extends EfectivoPipelineWithBreakContract
{
    private $newBalance;
    private $customer;
    private $billingClient;
    public function __construct( IBillingClient $billingClient)
    {
        $this->billingClient=$billingClient;
    }

    protected function stepProcess(BaseDTO $momoDTO)
    {
        
        if ($momoDTO->urlPrefix == 'lukanga' && \env('USE_RECEIPTING_MOCK') != "YES"){
            $momoDTO->stepProcessed=true;
            $this->newBalance="0";
            $this->customer = \json_decode(Cache::get($momoDTO->urlPrefix.
                                $momoDTO->accountNumber,\json_encode([])), true);
            if($this->customer){
                if(\key_exists('balance',$this->customer)){
                    $this->newBalance= (float)(\str_replace(",", "", $this->customer['balance'])) - 
                                            (float)$momoDTO->receiptAmount;
                    $this->newBalance= \number_format($this->newBalance, 2, '.', ',');
                }else{
                    $this->newBalance="0";
                }
            }else{
                $this->newBalance="0";
            }
            $receiptingParams=[ 
                'account' => $momoDTO->accountNumber,
                'reference' => $momoDTO->mnoTransactionId,
                'amount' => $momoDTO->receiptAmount,
                'mnoName'=>$momoDTO->mnoName,
                'balance' => $this->customer?(float)(\str_replace(",", "", $this->customer['balance'])):0
            ];

            $billingResponse=$this->billingClient->postPayment($receiptingParams);
            if($billingResponse['status']=='SUCCESS'){
                $momoDTO->receiptNumber=$billingResponse['receiptNumber'];
                $momoDTO->paymentStatus = 'RECEIPTED';
                $momoDTO->receipt = $this->formatReceipt($momoDTO->receiptNumber
                                        ,$momoDTO->receiptAmount,$momoDTO->accountNumber);
            }else{
                $momoDTO['error'] = "At post payment. ".$billingResponse['error'];
            }
        }
        return $momoDTO;

    }

    private function formatReceipt(string $receiptNumber, 
                $receiptAmount, string $accountNumber ): string
    {

        $receipt = "Payment successful\n" .
            "Rcpt No.: " . $receiptNumber . "\n" .
            "Amount: ZMW " . \number_format($receiptAmount, 2, '.', ',') . "\n".
            "Acc: " . $accountNumber . "\n";
            if($this->newBalance!="0"){
                $receipt.="Bal: ZMW ".$this->newBalance . "\n";
            }
        $receipt.="Date: " . Carbon::now()->format('d-M-Y') . "\n";
        return $receipt;

    }

}