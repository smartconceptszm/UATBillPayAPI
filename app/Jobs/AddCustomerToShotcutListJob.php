<?php

namespace App\Jobs;

use App\Http\Services\Payments\ShortcutCustomerService;
use App\Http\Services\Clients\ClientWalletService;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;
use App\Models\Payment;
use App\Jobs\BaseJob;

class AddCustomerToShotcutListJob extends BaseJob
{

   // public $timeout = 600;
   public function __construct(
      private BaseDTO $paymentDTO)
   {}

   public function handle(ShortcutCustomerService $shortcutCustomerService, ClientMenuService $clientMenuService,
                                 ClientWalletService $clientWalletService)
   {

      $menu = $clientMenuService->findById($this->paymentDTO->menu_id);
      
      if($menu->onOneAccount == "NO"){

         $customer = $shortcutCustomerService->findOneBy([
                                                         'customerAccount'=>$this->paymentDTO->customerAccount,
                                                         'mobileNumber' => $this->paymentDTO->mobileNumber
                                                      ]);

         if(!$customer){

            $theDate = Carbon::parse( $this->paymentDTO->created_at);
            $endDate = $theDate->copy()->endOfDay()->format('Y-m-d H:i:s');
            $startDate = $theDate->copy()->subMonths(3)->startOfMonth()->format('Y-m-d H:i:s');

            $paymentTransactions = Payment::where('customerAccount',$this->paymentDTO->customerAccount)
                                             ->where('walletNumber', $this->paymentDTO->mobileNumber)
                                             ->whereIn('paymentStatus',[PaymentStatusEnum::NoToken->value,
                                                            PaymentStatusEnum::Paid->value,
                                                            PaymentStatusEnum::Receipted->value,
                                                            PaymentStatusEnum::Receipt_Delivered->value])
                                             ->whereBetween('created_at', [$startDate, $endDate])
                                             ->count();
            
            if($paymentTransactions >1){
               $wallet = $clientWalletService->findById($this->paymentDTO->wallet_id);
               $shortcutCustomerService->create([
                                                'customerAccount'=>$this->paymentDTO->customerAccount,
                                                'mobileNumber' => $this->paymentDTO->mobileNumber,
                                                'client_id'=>$wallet->client_id
                                             ]);

               Log::info('('.$this->paymentDTO->urlPrefix.') Customer added to shortcut list. Mobile Number = '.$this->paymentDTO->mobileNumber.
                                             '. Customer Account = '.$this->paymentDTO->customerAccount);
            }

         }

      }

   }

   /**
     * Prevent the job from being saved in the failed_jobs table
   */
   public function failed(\Throwable $exception)
   {
      Log::error($exception->getMessage());
   }

}