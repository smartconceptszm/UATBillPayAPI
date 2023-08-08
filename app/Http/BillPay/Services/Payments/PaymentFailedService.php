<?php

namespace App\Http\BillPay\Services\Payments;

use App\Http\BillPay\Repositories\Payments\PaymentToReviewRepo;
use App\Http\BillPay\Repositories\Payments\PaymentFailedRepo;
use App\Http\BillPay\Services\Contracts\IFindAllService;
use App\Http\BillPay\Services\Contracts\IUpdateService;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ReConfirmMoMoPaymentJob;
use Illuminate\Support\Facades\Auth;
use App\Http\BillPay\DTOs\MoMoDTO;
use Illuminate\Support\Carbon;
use Exception;

class PaymentFailedService implements IFindAllService,IUpdateService
{

   private $paymentFailedRepo;
   private $repository;
   private $momoDTO;
   public function __construct(PaymentFailedRepo $paymentFailedRepo,
      PaymentToReviewRepo $repository,
      MoMoDTO $momoDTO)
   {
      $this->paymentFailedRepo = $paymentFailedRepo;
      $this->repository = $repository;
      $this->momoDTO = $momoDTO;
   }

   public function findAll(array $criteria=null, array $fields = ['*']):array|null{
      try {
         $user = Auth::user(); 
         $criteria['client_id'] = $user->client_id;
         $response = $this->paymentFailedRepo->findAll($criteria, $fields);
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $response;
   }

   public function update(array $data, string $id):object{

      try {
         $thePayment = $this->repository->findById($id);
         $momoDTO = $this->momoDTO->fromArray(\get_object_vars($thePayment));
         $user = Auth::user(); 
         $momoDTO->user_id = $user->id;
         $momoDTO->error = "";
         Queue::later(Carbon::now()->addMinutes((int)\env('PAYMENT_REVIEW_DELAY')),
                                                   new ReConfirmMoMoPaymentJob($momoDTO));
         $response = (object)[
                           'data' => 'Payment of ZMW '.$momoDTO->paymentAmount." on Account ".
                                       $momoDTO->accountNumber." submitted for review. Check status after a while"      
                     ];
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $response;
      
   }

}
