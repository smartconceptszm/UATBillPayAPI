<?php

namespace App\Http\Services\Promotions;


use App\Http\Services\Promotions\ProcessPromotionService;
use App\Http\Services\Payments\PaymentToReviewService;
use App\Http\Services\Promotions\PromotionService;
use App\Http\Services\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\DB;
use App\Http\DTOs\PromotionDTO;
use App\Http\DTOs\MoMoDTO;

use Exception;

class PromotionEntriesNotProcessedService
{

   public function __construct(
      private ProcessPromotionService $processPromotionService,
      private PaymentToReviewService $paymentToReviewService,
      private PromotionService $promotionService,
      private PromotionDTO $promotionDTO,
      private MoMoDTO $paymentDTO
   ) {}


   public function findAll(array $criteria):array|null
   {

      try {

         $dto=(object)$criteria;
         $dto->dateFrom = $dto->dateFrom." 00:00:00";
         $dto->dateTo = $dto->dateTo." 23:59:59";

         $thePromotion = $this->promotionService->findById($dto->promotion_id);

         $records = DB::table('payments as p')
                        ->join('client_wallets as cw', 'p.wallet_id', '=', 'cw.id')
                        ->join('payments_providers as pp','cw.payments_provider_id','=','pp.id')
                        ->join('clients as c', 'c.id', '=', 'cw.client_id')
                        ->join('client_menus as cm', 'cm.id', '=', 'p.menu_id')
                        ->leftJoin('promotion_entries as pe', 'p.id', '=', 'pe.payment_id')
                        ->select('p.id','p.customerAccount','p.created_at','p.mobileNumber','cm.prompt as paymentType',
                                    'p.receiptAmount','p.receiptNumber','p.tokenNumber','p.paymentStatus')
                        ->whereIn('p.paymentStatus', 
                                 [PaymentStatusEnum::Receipted->value,PaymentStatusEnum::Receipt_Delivered->value]);
         if($thePromotion->consumerType !== "ALL"){
            $records = $records->where('p.consumerType', '=', $thePromotion->consumerType);
         }

         $records = $records->where('p.paymentAmount','>=',$thePromotion->entryAmount)
                        ->where('p.created_at', '>=', $dto->dateFrom)
                        ->where('p.created_at', '<=', $dto->dateTo)
                        ->where('c.id', $dto->client_id)
                        ->whereNull('pe.id');

         $theSQLQuery = $records->toSql();
         $theBindings = $records-> getBindings();
         $rawSql = vsprintf(str_replace(['?'], ['\'%s\''], $theSQLQuery), $theBindings);

         $records = $records->get();

         return $records->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }


   public function processEntry(string $payment_id, string $promotion_id):string
   {
      try {

         $thePayment = $this->paymentToReviewService->findById($payment_id);
         $paymentDTO = $this->paymentDTO->fromArray(\get_object_vars($thePayment));
         $activePromotion = $this->promotionService->findById($promotion_id);

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
         $promotionDTO = $this->processPromotionService->handle($promotionDTO);
         return $promotionDTO->message;

      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }





}
