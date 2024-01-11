<?php

namespace App\Http\Services\Payments;

use App\Http\Services\Payments\PaymentToReviewService;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ReConfirmMoMoPaymentJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Http\DTOs\MoMoDTO;
use Exception;

class PaymentFailedService
{

   public function __construct(
      private PaymentToReviewService $paymentToReviewService,
      private MoMoDTO $momoDTO)
   {}

   public function findAll(array $criteria=null):array|null{
      try {
         $user = Auth::user(); 
         $criteria['client_id'] = $user->client_id;
         $dto=(object)$criteria;
         $records = DB::table('payments as p')
                  ->join('sessions as s','p.session_id','=','s.id')
                  ->join('mnos','p.mno_id','=','mnos.id')
                  ->join('client_menus as m','p.menu_id','=','m.id')
                  ->select('p.id','p.created_at','p.mobileNumber','p.accountNumber','p.receiptNumber',
                           'p.receiptAmount','p.paymentAmount','p.transactionId','p.district',
                           'p.mnoTransactionId','mnos.name as mno','m.prompt as paymentType',
                           'p.paymentStatus','p.error')
                  ->whereIn('p.paymentStatus', ['SUBMISSION FAILED','PAYMENT FAILED'])
                  ->where('p.client_id', '=', $dto->client_id)
                  ->orderByDesc('p.created_at');
         if($dto->dateFrom && $dto->dateTo){
               $records =$records->whereDate('p.created_at', '>=', $dto->dateFrom)
                                 ->whereDate('p.created_at', '<=', $dto->dateTo);
         }
         $allFailed = $records->get();
         $providerErrors = $allFailed->filter(
               function ($item) {
                  if ((\strpos($item->error,"Status Code"))
                        || (\strpos($item->error,"on get transaction status"))
                        || (\strpos($item->error,"Get Token error"))
                        || (\strpos($item->error,"on collect funds"))) 
                  {
                        return $item;
                  }
               }
            )->values();
         return [
               'all' => $allFailed,
               'providerErrors' => $providerErrors,
            ];

      } catch (Exception $e) {
         throw new Exception($e->getMessage());
      }

   }

   public function update(string $id):string{

      try {
         $thePayment = $this->paymentToReviewService->findById($id);
         $momoDTO = $this->momoDTO->fromArray(\get_object_vars($thePayment));
         $user = Auth::user(); 
         $momoDTO->user_id = $user->id;
         $momoDTO->error = "";
         Queue::later(Carbon::now()->addMinutes((int)\env('PAYMENT_REVIEW_DELAY')),
                                                new ReConfirmMoMoPaymentJob($momoDTO));
         return 'Payment of ZMW '.$momoDTO->paymentAmount." on Account ".
                        $momoDTO->accountNumber." submitted for review. Check status after a short while";
      } catch (Exception $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
