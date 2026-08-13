<?php

namespace App\Http\Services\Promotions;

use App\Http\Services\Promotions\RaffleDrawService;
use App\Http\Services\Promotions\PromotionService;
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
            $data['dateFrom']= $theDate->copy()->startOfMonth()->format('Y-m-d');
            $data['dateTo'] = $theDate->copy()->endOfMonth()->format('Y-m-d');
         }

         $theDraws = $this->raffleDrawService->findAll([
               'promotion_id' => $thePromotion->id
         ]);
         
         $dateFrom = Carbon::parse($data['dateFrom']);
         $dateTo   = Carbon::parse($data['dateTo']);
         
         $activeDraw = null;

         foreach ($theDraws as $theDraw) {
               $start = Carbon::parse($theDraw->drawStart);
               $end   = Carbon::parse($theDraw->drawEnd);
         
               $fromInRange = $dateFrom->between($start, $end);
               $toInRange   = $dateTo->between($start, $end);
               $drawOverlapped = ($dateFrom<$start && $dateTo>$end);
         
               if (!($fromInRange || $toInRange || $drawOverlapped)) {
                  continue;
               }
         
               // A draw already exists within this range
               $activeDraw = $theDraw;
               $drawNumber = (int) $theDraw->numberOfDraws;
         
               // Check draw limit
               if ($drawNumber === (int) $thePromotion->raffleDrawLimit) {
                  return (object)[
                     'status' => 'FAIL',
                     'drawNumber' => $drawNumber + 1,
                     'message' => 'Raffle already drawn for the specified period!'
                  ];
               }
         
               // If within the same range, allow continuation
               if ($dateFrom->equalTo($start) && $dateTo->equalTo($end)) {
                  break;
               }
         
               // Otherwise, wrong period
               return (object)[
                  'status' => 'FAIL',
                  'drawNumber' => $drawNumber,
                  'message' => 'Please select the exact period as the last draw!'
               ];
         }

         if($activeDraw){
            $drawNumber = (int)$activeDraw->numberOfDraws +1;
         }else{
            $drawNumber = 1;
         }

         return (object)['status'=>"PASS",
                              'drawNumber'=> $drawNumber,
                              'message'=>"Are you sure you want to draw raffle winner for the selected period?"];

      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

}


