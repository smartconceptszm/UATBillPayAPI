<?php

namespace App\Http\Services\Sessions;

use Illuminate\Support\Facades\DB;
use App\Http\DTOs\BaseDTO;
use Exception;

class SessionHistoryService
{

   public function getLatestIncompletePayment(BaseDTO $txDTO):object|null
   {

      try {
         $startOfDay = $txDTO->created_at->copy()->startOfDay();
         $startOfDay = $startOfDay->format('Y-m-d H:i:s');
         $record = DB::table('sessions as s')
                        ->join('client_menus as cm','s.menu_id','=','cm.id')
                        ->select('s.menu_id','s.customerJourney', 's.customerAccount','s.sessionId',
                                 'cm.prompt','s.revenuePoint','s.paymentAmount','s.response','s.status',
                                 'cm.billingClient','s.error')
                        ->where('s.mobileNumber', '=', $txDTO->mobileNumber)
                        ->where('s.sessionId', '!=', $txDTO->sessionId)
                        ->where('s.client_id', '=', $txDTO->client_id)
                        ->where('s.created_at', '>', $startOfDay)
                        ->whereIn('s.status', ["INITIATED","SYSTEMERROR"])
                        ->where('cm.isPayment', '=', 'YES')
                        ->orderByDesc('s.created_at')
                        ->first();
         return $record;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

}
