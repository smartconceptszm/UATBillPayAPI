<?php

namespace App\Http\Services\Web\Payments;

use Illuminate\Support\Facades\DB;
use Exception;

class PaymentSessionService
{

   public function findAll(array $criteria):array|null{
      try {
         $dto = (object)$criteria;
         $records = DB::table('sessions as s')
            ->join('mnos','s.mno_id','=','mnos.id')
            ->join('client_menus as m','s.menu_id','=','m.id')
            ->leftJoin('payments as p','p.session_id','=','s.id')
            ->leftJoin('client_wallets as cw','p.wallet_id','=','cw.id')
            ->leftJoin('payments_providers as pps','cw.payments_provider_id','=','pps.id')
            ->select('s.id as session_id','s.sessionId','s.client_id','s.customerJourney','s.mobileNumber','s.accountNumber',
                        's.meterNumber','s.district','s.status','s.created_at','p.id','p.reference',
                        'p.ppTransactionId','p.surchargeAmount','p.paymentAmount','p.receiptAmount',
                        'p.transactionId','p.receiptNumber','p.tokenNumber','p.receipt','p.channel',
                        'p.paymentStatus','p.error','m.accountType','m.description as paymentType',
                        'mnos.name as mno', 'pps.shortName as paymentProvider');
         if(\array_key_exists('accountNumber',$criteria)){
            $records = $records->where('s.accountNumber', '=', $dto->accountNumber);
         }
         if(\array_key_exists('mobileNumber',$criteria)){
            $records = $records->where('s.mobileNumber', '=', $dto->mobileNumber);
         }
         if(\array_key_exists('meterNumber',$criteria)){
            $records = $records->where('s.meterNumber', '=', $dto->meterNumber);
         }
         if(\array_key_exists('dateFrom',$criteria) && \array_key_exists('dateTo',$criteria)){
            $records =$records->whereBetween('s.created_at', [$dto->dateFrom." 00:00:00", $dto->dateTo." 23:59:59"]);
         }
         $records = $records->where('s.client_id', '=', $dto->client_id)
                              ->where('m.isPayment','=', 'YES')
                              ->orderByDesc('s.created_at');
         // $theSQLQuery = $records->toSql();
         // $theBindings = $records-> getBindings();
         // $rawSql = vsprintf(str_replace(['?'], ['\'%s\''], $theSQLQuery), $theBindings);
         return $records->get()->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

}
