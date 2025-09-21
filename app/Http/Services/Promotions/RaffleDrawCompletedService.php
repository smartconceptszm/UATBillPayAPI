<?php

namespace App\Http\Services\Promotions;

use App\Http\Services\Promotions\RaffleDrawService;
use App\Http\Services\Promotions\PromotionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class RaffleDrawCompletedService
{

   public function __construct(
      private RaffleDrawService $raffleDrawService,
      private PromotionService $promotionService
   ) {}


   public function handle(array $data) : object|null {


      try {

         $thePromotion = $this->promotionService->findById($data['promotion_id']);
         if($thePromotion->raffleDrawType == "MONTHLY"){
            $theDate = Carbon::createFromFormat('Y-m-d', $data['theMonth']."-01");
            $drawStart = $theDate->copy()->startOfMonth();
            $drawEnd = $theDate->copy()->endOfMonth();
         }else{
            $drawStart = $data['drawStart'];
            $drawEnd = $data['drawEnd'];
         }

         $theDraw = $this->raffleDrawService->findOneBy([
                                                      'promotion_id' => $thePromotion->id,
                                                      'drawStart' => $drawStart->copy()->format('Y-m-d'),
                                                      'drawEnd' => $drawEnd->copy()->format('Y-m-d'),
                                                   ]);

         if($theDraw && (int)$theDraw->numberOfDraws == (int)$thePromotion->raffleDrawLimit){
            return (object)['status'=>"FAIL",
                              'drawNumber'=> (int)$theDraw->numberOfDraws +1,
                              'message'=>"Raffle already drawn for the specified period!"];
         }else{

            if($theDraw){
               $drawNumber = (int)$theDraw->numberOfDraws +1;
            }else{
               $drawNumber = 1;
            }

            return (object)['status'=>"PASS",
                              'drawNumber'=> $drawNumber,
                              'message'=>"Are you sure you want to draw raffle winner for the selected period?"];
         }

      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

}


