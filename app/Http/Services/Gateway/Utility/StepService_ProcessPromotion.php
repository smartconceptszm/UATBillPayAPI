<?php

namespace App\Http\Services\Gateway\Utility;

use App\Http\Services\Clients\ClientWalletService;
use App\Http\Services\Promotions\PromotionService;
use App\Jobs\ProcessPromotionJob;
use App\Http\DTOs\PromotionDTO;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class StepService_ProcessPromotion
{

   public function __construct(
      private ClientWalletService $clientWalletService,
		private PromotionService $promotionService,
      private PromotionDTO $promotionDTO) 
	{}

   public function handle(BaseDTO $paymentDTO)
   {

      try {
         $clientWallet = $this->clientWalletService->findById($paymentDTO->wallet_id);
         $activePromotion = $this->promotionService->findActivePromotion($clientWallet->client_id, $paymentDTO->consumerType);
         if($activePromotion){
            $promotionDTO = $this->promotionDTO->fromArray($paymentDTO->toArray());
            $promotionDTO->promotionRaffleEntryMessage = $activePromotion->raffleEntryMessage;
            $promotionDTO->promotionRaffleEntryAmount = $activePromotion->raffleEntryAmount;
            $promotionDTO->promotionConsumerType = $activePromotion->consumerType;
            $promotionDTO->promotionEntryMessage = $activePromotion->entryMessage;
            $promotionDTO->promotionEntryAmount = $activePromotion->entryAmount;
            $promotionDTO->promotionRateValue = $activePromotion->rateValue;
            $promotionDTO->receiptNumber = $paymentDTO->receiptNumber;
            $promotionDTO->promotionOnDebt = $activePromotion->onDebt;
            $promotionDTO->consumerType = $paymentDTO->consumerType;
            $promotionDTO->client_id = $activePromotion->client_id;
            $promotionDTO->promotionName = $activePromotion->name;
            $promotionDTO->promotionType = $activePromotion->type;
            $promotionDTO->promotion_id = $activePromotion->id;
            $promotionDTO->menu_id = $paymentDTO->menu_id;
            $promotionDTO->mno_id = $paymentDTO->mno_id;
            $promotionDTO->payment_id = $paymentDTO->id;

            ProcessPromotionJob::dispatch($promotionDTO)
                                 ->delay(Carbon::now()->addSeconds(20))
                                 ->onQueue('low');
         }
      } catch (\Throwable $e) {
         $paymentDTO->error = 'At process promotion. ' . $e->getMessage();
      }
      return $paymentDTO;
   }

}