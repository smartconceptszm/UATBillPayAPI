<?php

namespace App\Http\Services\Promotions;

use App\Http\Services\Promotions\RaffleDrawEntryService;
use App\Http\Services\Promotions\RaffleDrawService;
use App\Http\Services\Promotions\PromotionService;
use App\Http\Services\Clients\ClientService;
use Illuminate\Support\Carbon;
use App\Jobs\SendSMSesJob;
use Exception;

class RaffleWinnerService
{

   public function __construct(
      private RaffleDrawEntryService $raffleDrawEntryService,
      private RaffleDrawService $raffleDrawService,
      private PromotionService $promotionService,
      private ClientService $clientService
   ) {}


   public function handle(array $data) : object|null {
      try {
         
         $thePromotion = $this->promotionService->findById($data['promotion_id']);
         if($thePromotion->raffleDrawType == "MONTHLY"){
            $theDate = Carbon::createFromFormat('Y-m-d', $data['theMonth']."-01");
            $theYear = (string)$theDate->year;
            $theMonth = \strlen((string)$theDate->month)==2?$theDate->month:"0".(string)$theDate->month;
            $theDay = \strlen((string)$theDate->day)==2?$theDate->day:"0".(string)$theDate->day;
            $drawStart = $theDate->copy()->startOfMonth();
            $drawEnd = $theDate->copy()->endOfMonth();
         }else{
            $theDate = Carbon::createFromFormat('Y-m-d', $data['drawStart']);
            $theYear = (string)$theDate->year;
            $theMonth = \strlen((string)$theDate->month)==2?$theDate->month:"0".(string)$theDate->month;
            $theDay = \strlen((string)$theDate->day)==2?$theDate->day:"0".(string)$theDate->day;
            $drawStart = $data['drawStart'];
            $drawEnd = $data['drawEnd'];
         }

         $drawData = [];
         $drawData['promotion_id'] = $data['promotion_id'];
         $drawData['dateOfDraw'] = $data['dateOfDraw'];
         $drawData['year'] = $theYear;
         $drawData['month'] = $theMonth;
         $drawData['day'] =  $theDay;
         $drawData['drawStart'] = $drawStart->copy()->format('Y-m-d');
         $drawData['drawEnd'] = $drawEnd->copy()->format('Y-m-d');
         $drawData['numberOfDraws'] = $data['drawNumber'];
         if($data['drawNumber'] == 1){
            $theRaffleDraw = $this->raffleDrawService->create($drawData);
         }else{
            $theRaffleDraw = $this->raffleDrawService->findOneBy([
                                                            'promotion_id' => $thePromotion->id,
                                                            'drawStart' => $drawStart->copy()->format('Y-m-d'),
                                                            'drawEnd' => $drawEnd->copy()->format('Y-m-d')
                                                         ]);
            if($theRaffleDraw->numberOfDraws >= (int) $data['drawNumber']){
               throw New Exception("Maximum number of draws already reached!");
            }
            $theRaffleDraw = $this->raffleDrawService->update($drawData,$theRaffleDraw->id);
         }

         $winnerData = [
                           'raffleDate' =>$data['dateOfDraw'],
                           'drawNumber' => $data['drawNumber'],
                           'winMessage'=> sprintf($thePromotion->raffleWinnerMessage,
                                    \number_format((float)$data['drawNumber'],0, '.', ',')),
                           'status' => "WINNER",
                  ];

         $this->raffleDrawEntryService->update($winnerData,$data['drawWinner']['id']);

         //Send SMS to Winner
            $theClient = $this->clientService->findById($thePromotion->client_id);
            $smses = [[
                           'mobileNumber' => $data['drawWinner']['mobileNumber'],
                           'client_id' => $theClient->id,
                           'urlPrefix'=>$theClient->urlPrefix,
                           'message' => $winnerData['winMessage'],
                           'type' => "NOTIFICATION",
                        ]];
                        
            SendSMSesJob::dispatch($smses)
                     ->delay(Carbon::now()->addSeconds(1))
                     ->onQueue('high');
         //

         return $theRaffleDraw;
         
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

}


