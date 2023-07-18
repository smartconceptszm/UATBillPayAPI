<?php

namespace App\Http\BillPay\Services\MoMo\BillingClientCallers;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\External\BillingClients\IBillingClient;
use App\Http\BillPay\Services\MenuConfigs\OtherPaymentTypeService;
use Illuminate\Support\Facades\Cache;
use App\Http\BillPay\DTOs\BaseDTO;
use Illuminate\Support\Carbon;

class PostPaymentSwasco extends EfectivoPipelineWithBreakContract
{

    private $billingClient;
    private $otherPayTypes;
    public function __construct(OtherPaymentTypeService $otherPayTypes,
        IBillingClient $billingClient)
    {
        $this->otherPayTypes = $otherPayTypes;
        $this->billingClient=$billingClient;
    }

    protected function stepProcess(BaseDTO $momoDTO)
    {

        if ($momoDTO->urlPrefix == 'swasco' && \env('USE_RECEIPTING_MOCK') != "YES"){
            $momoDTO->stepProcessed=true;
            $customer=null;
            $txAccountNumber="";
            $paymentType = "1";
            $thePaymentType=null;
            $receiptAccount='';
            $newBalance="0";
            //Trimmed to 20 cause of constraint on API
            $swascoTransactionRef = \strlen($momoDTO->sessionId) > 20 ? 
                                        \substr($momoDTO->sessionId, 0, 20) : $momoDTO->sessionId;
            
            if ($momoDTO->menu == 'PayBill'){
                $txAccountNumber=$momoDTO->accountNumber;
                $receiptAccount=$txAccountNumber;
                $customer = \json_decode(Cache::get($momoDTO->urlPrefix.
                                            $momoDTO->accountNumber,\json_encode([])), true);
                if($customer){
                    if(\key_exists('balance',$customer)){
                        $newBalance = (float)(\str_replace(",", "", $customer['balance'])) - 
                                                (float)$momoDTO->receiptAmount;
                        $newBalance = \number_format($newBalance, 2, '.', ',');
                    }else{
                        $newBalance="0";
                    }
                }else{
                    $newBalance="0";
                }
            }else{
                $arrCustomerJourney = \explode("*", $momoDTO->customerJourney);
                $thePaymentType = $this->otherPayTypes->findOneBy([
                            'client_id' => $momoDTO->client_id,
                            'order' => $arrCustomerJourney[2]
                      ]);

                $paymentType = $thePaymentType->code;
                if ($thePaymentType->receiptAccount == 'CUSTOMER') {
                    $txAccountNumber = $momoDTO->accountNumber;
                    $receiptAccount = $txAccountNumber;
                } 
                if ($thePaymentType['accountType']!='Customer' && $thePaymentType['hasApplicationNo']) {
                    $txAccountNumber=$arrCustomerJourney[3];
                    $receiptAccount=$thePaymentType['accountNo'];
                } 
                if ($thePaymentType->receiptAccount == 'CUSTOMER' && !($thePaymentType->hasApplicationNo = 'YES')) {
                    //e.g. Vacuum Tanker/Pit Emptying Services
                    $swascoTransactionRef = \strlen($arrCustomerJourney[3]) > 20 ? 
                                        \substr($arrCustomerJourney[3], 0, 20) :$arrCustomerJourney[3];
                    $txAccountNumber = $momoDTO->accountNumber;
                    $receiptAccount = $thePaymentType->ledgerAccountNumber;
                } 
            }

            $receiptingParams=[ 
                'paymentType'=>$paymentType,
                'account' => $txAccountNumber,
                'amount' => $momoDTO->receiptAmount,
                'mobileNumber"'=> $momoDTO->mobileNumber,
                'referenceNumber' => $swascoTransactionRef,
            ];

            $billingResponse=$this->billingClient->postPayment($receiptingParams);
            if($billingResponse['status']=='SUCCESS'){
                $momoDTO->receiptNumber = $billingResponse['receiptNumber'];
                $momoDTO->paymentStatus = "RECEIPTED";
                $formatReceiptParams=[
                    'receiptAccount'=>$receiptAccount,
                    'paymentType'=>$paymentType,
                    'thePaymentType'=>$thePaymentType,
                    'txAccountNumber'=>$txAccountNumber,
                    'transactionRef'=>$swascoTransactionRef,
                    'customer'=>$customer,
                    'newBalance'=>$newBalance
                ];
                $momoDTO->receipt = $this->formatReceipt($momoDTO->receiptNumber,
                                                $momoDTO->receiptAmount,$formatReceiptParams);
            }else{
                $momoDTO->error = "At post payment. ".$billingResponse['error'];
            }
        }
        return $momoDTO;

    }

    private function formatReceipt(string $receiptNumber,$receiptAmount, 
                                            array $formatReceiptParams): string
    {

        $receipt = "Payment successful\n" .
        "Rcpt No.: " . $receiptNumber . "\n" .
        "Amount: ZMW " . \number_format($receiptAmount, 2, '.', ',') . "\n".
        "Acc: " . $formatReceiptParams['receiptAccount'] . "\n";
        if($formatReceiptParams['paymentType']!='1'){
            if(($formatReceiptParams['thePaymentType']['accountType']=='G/L Account') &&
                ($formatReceiptParams['thePaymentType']['hasApplicationNo'])){
                    $receipt.="Ref: " .
                    \str_replace(\chr(47), "", $formatReceiptParams['txAccountNumber']). "\n";
            }else{
                $receipt.="Ref: " . 
                    \str_replace(\chr(47), "", $formatReceiptParams['transactionRef'] ). "\n";
            }
        }
        if($formatReceiptParams['newBalance']!="0"){
            $receipt.="Bal: ZMW ". $formatReceiptParams['newBalance'] . "\n";
        }
        $receipt.="Date: " . Carbon::now()->format('d-M-Y') . "\n";
        return $receipt;

    }

}