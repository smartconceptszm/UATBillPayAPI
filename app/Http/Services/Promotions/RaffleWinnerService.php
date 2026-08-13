<?php

namespace App\Http\Services\Promotions;

use App\Http\Services\Promotions\RaffleDrawEntryService;
use App\Http\Services\Promotions\RaffleDrawService;
use App\Http\Services\Promotions\PromotionService;
use App\Http\Services\Clients\ClientService;
use Illuminate\Support\Facades\Auth;
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
         $theClient = $this->clientService->findById($thePromotion->client_id);

         if($thePromotion->raffleDrawType == "MONTHLY"){
            $theDate = Carbon::createFromFormat('Y-m-d', $data['theMonth']."-01");
            $drawStart = $theDate->copy()->startOfMonth()->format('Y-m-d');
            $drawEnd = $theDate->copy()->endOfMonth()->format('Y-m-d');
         }else{
            $drawStart = $data['drawStart'];
            $drawEnd =  $data['drawEnd'];
         }

         $drawData = [];
         $drawData['promotion_id'] = $data['promotion_id'];
         $drawData['dateOfDraw'] = $data['dateOfDraw'];
         $drawData['year'] = \substr($data['dateOfDraw'],0,4);
         $drawData['month'] = \substr($data['dateOfDraw'],5,2);
         $drawData['day'] =  \substr($data['dateOfDraw'],8,2);
         $drawData['drawStart'] = $drawStart;
         $drawData['drawEnd'] = $drawEnd;
         $drawData['numberOfDraws'] = $data['drawNumber'];

         $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
         if($billpaySettings['RAFFLE_MOCK_'.\strtoupper($theClient->urlPrefix)] == "YES"){
            $user = Auth::user(); 
            //SMS for Winner
               $smses = [[
                  'mobileNumber' => $user->mobileNumber,
                  'client_id' => $theClient->id,
                  'urlPrefix'=>$theClient->urlPrefix,
                  'message' => "MOCK: ".$thePromotion->raffleWinnerMessage,
                  'type' => "NOTIFICATION",
               ]];
            //
            $theRaffleDraw = (object)$drawData;
         }else{
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
                              'winMessage'=> $thePromotion->raffleWinnerMessage,
                              'status' => "WINNER",
                     ];

                     // 'winMessage'=> sprintf($thePromotion->raffleWinnerMessage,
                     // \number_format((float)$data['drawNumber'],0, '.', ',')),

            $this->raffleDrawEntryService->update($winnerData,$data['drawWinner']['id']);

            //SMS for Winner
               $smses = [[
                              'mobileNumber' => $data['drawWinner']['mobileNumber'],
                              'client_id' => $theClient->id,
                              'urlPrefix'=>$theClient->urlPrefix,
                              'message' => $winnerData['winMessage'],
                              'type' => "NOTIFICATION",
                           ]];
            //
         }    

         SendSMSesJob::dispatch($smses)
                  ->delay(Carbon::now()->addSeconds(1))
                  ->onQueue('high');
      
         
         return $theRaffleDraw;
         
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

}


