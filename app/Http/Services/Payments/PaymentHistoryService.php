<?php

namespace App\Http\Services\Payments;

use App\Http\Services\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\DB;

use Exception;

class PaymentHistoryService
{

   public function findByCustomerAccount(array $criteria):array|null
   {

      try {
         $dto = (object)$criteria;
         $records = DB::table('payments as p')
                        ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                        ->join('client_menus as cm','p.menu_id','=','cm.id')
                        ->select('p.id','p.created_at','p.customerAccount','p.mobileNumber',
                                             'cm.prompt','p.receiptAmount','p.receiptNumber')
                        ->where('customerAccount', '=', $dto->customerAccount)
                        ->whereIn('paymentStatus',[PaymentStatusEnum::NoToken->value,
                                                         PaymentStatusEnum::Paid->value,
                                                         PaymentStatusEnum::Receipted->value,
                                                         PaymentStatusEnum::Receipt_Delivered->value])
                        ->where('cw.client_id', '=', $dto->client_id)
                        ->orderByDesc('created_at')
                        ->limit($dto->limit)
                        ->get();
         return $records->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

   public function findByWallet(array $criteria):array|null
   {

      try {
         $dto = (object)$criteria;
         $records = DB::table('payments as p')
                        ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                        ->join('client_menus as cm','p.menu_id','=','cm.id')
                        ->select('p.id','p.created_at','p.customerAccount','p.mobileNumber',
                                             'cm.prompt','p.receiptAmount','p.receiptNumber')
                        ->where('mobileNumber', '=', $dto->mobileNumber)
                        ->whereIn('paymentStatus',[PaymentStatusEnum::NoToken->value,
                                                         PaymentStatusEnum::Paid->value,
                                                         PaymentStatusEnum::Receipted->value,
                                                         PaymentStatusEnum::Receipt_Delivered->value])
                        ->where('cw.client_id', '=', $dto->client_id)
                        ->orderByDesc('created_at')
                        ->limit($dto->limit)
                        ->get();
         return $records->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

   public function findForThisMonth(array $criteria):array|null
   {

      try {


         // $record = DB::table('promotions')
         //                ->select('*')
         //                ->where('client_id', '=', $client_id)
         //                ->where('status', '=', "ACTIVE")
         //                ->whereDate('startDate','<=',Carbon::now())
         //                ->whereDate('endDate','>=',Carbon::now())
         //                ->first();
         // return $record;

         $dto = (object)$criteria;
         $records = DB::table('payments as p')
                        ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                        ->join('client_menus as cm','p.menu_id','=','cm.id')
                        ->select('p.id','p.receiptAmount')
                        ->where('customerAccount', '=', $dto->customerAccount)
                        ->whereIn('paymentStatus',[PaymentStatusEnum::NoToken->value,
                                                         PaymentStatusEnum::Paid->value,
                                                         PaymentStatusEnum::Receipted->value,
                                                         PaymentStatusEnum::Receipt_Delivered->value])
                        ->where('cw.client_id', '=', $dto->client_id)
                        ->orderByDesc('created_at')
                        ->limit($dto->limit)
                        ->get();
         return $records->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

   public function getLatestToken(array $criteria):object|null
   {

      try {
         $dto = (object)$criteria;
         $record = DB::table('payments as p')

                        ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                        ->join('clients as c','cw.client_id','=','c.id')

                        ->join('client_menus as cm','p.menu_id','=','cm.id')

                        ->select('p.id','p.tokenNumber', 'p.receipt')

                        ->where('p.customerAccount', '=', $dto->customerAccount)
                        ->whereIn('p.paymentStatus',[PaymentStatusEnum::Paid->value,
                                                         PaymentStatusEnum::Receipted->value,
                                                         PaymentStatusEnum::Receipt_Delivered->value])
                        ->where('c.id', '=', $dto->client_id)
                        ->where('cm.isPayment', '=', "YES")
                        ->where('cm.paymentType', '=', "PRE-PAID")
                        ->where('cm.isDefault', '=', "YES")

                        ->orderByDesc('p.created_at')
                        ->first();
         $record = \is_null($record)?null:(object)$record->toArray();
         return $record;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

}