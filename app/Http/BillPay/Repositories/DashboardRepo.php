<?php

namespace App\Http\BillPay\Repositories;

use App\Http\BillPay\Repositories\Contracts\IFindAllRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class DashboardRepo implements IFindAllRepository
{

    public function findAll(array $criteria = null, array $fields = ['*']):array
    {

        try {
            $dto = (object)$criteria;
            //Get all in Date Range
            $thePayments = DB::table('payments as p')
                ->join('mnos as m','p.mno_id','=','m.id')
                ->join('clients as c','p.client_id','=','c.id')
                ->select('p.id','c.shortName','p.receiptAmount','p.paymentAmount',
                            'p.surchargeAmount','m.name as mno','m.colour')
                ->whereIn('p.paymentStatus', 
                    ['PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED'])
                ->whereDate('p.created_at', '>=', $dto->dateFrom)
                ->whereDate('p.created_at', '<=', $dto->dateTo)
                ->get();

            $groupedData = $thePayments->groupBy('shortName');
            $byClient=[];
            foreach ($groupedData as $key => $value) {
                $byClient[]= [
                    "district"=>$key,
                    "totalRevenue"=>$value->sum('paymentAmount')
                    ];
            }

            $groupedData= $thePayments->groupBy('mno');
            $byMNO=[];
            foreach ($groupedData as $key => $value) {
                $firstRow=$value->first();
                $byMNO[]= [
                    "mno"=>$key,
                    "colour"=>$firstRow->colour,
                    "totalRevenue"=>$value->sum('paymentAmount')
                    ];
            }


            // Get all Days in Current Month
                $theDateTo = Carbon::parse($dto->dateTo);
                $currentYear = $theDateTo->format('Y');
                $currentMonth = $theDateTo->format('m');
                $firstDayOfCurrentMonth = $currentYear . '-' . $currentMonth . '-01';
                $dailyTrends = DB::table('payments as p')
                    ->select(DB::raw('dayofmonth(p.created_at) as dayOfTx,
                                        COUNT(p.id) AS noOfPayments,
                                            SUM(p.receiptAmount) as totalAmount'))
                    ->whereIn('p.paymentStatus', ['PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED'])
                    ->whereDate('p.created_at', '>=', $firstDayOfCurrentMonth)
                    ->whereDate('p.created_at', '<=', $theDateTo)
                    ->groupBy('dayOfTx')
                    ->orderBy('dayOfTx')
                    ->get();
            //

            // Get collection over last one year
                $theDateFrom = Carbon::parse($dto->dateFrom);
                $startDate  = $theDateFrom->subYear(1);
                $myYear = $startDate->format('Y');
                $myMonth = $startDate->format('m');
                $myMonth = (int)$myMonth +1;
                if($myMonth>12){
                    $myMonth=1;
                    $myYear = (int)$myYear + 1;
                }
                $myMonth = $myMonth > 9? $myMonth:"0".$myMonth;
                $dateFrom = $myYear . '-' . $myMonth . '-01';
                $paymentsTrends =  DB::table('payments as p')
                    ->join('mnos as m','p.mno_id','=','m.id')
                    ->select(DB::raw('SUM(p.receiptAmount) as totalAmount, 
                                        month(p.created_at) as monthOfTx,
                                        m.name as mno,m.colour'))
                    ->whereIn('p.paymentStatus', ['PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED'])
                    ->whereDate('p.created_at', '>=', $dateFrom)
                    ->whereDate('p.created_at', '<=', $dto->dateTo)
                    ->groupBy('monthOfTx', 'mno', 'colour')
                    ->orderBy('mno')
                    ->get();
                $response=[
                                        'payments' => $byMNO,
                                        'byDistrict' => $byClient,
                                        'paymentsTrends' => $paymentsTrends->toArray(),
                                        'dailyTrends' => $dailyTrends->toArray(),
                                    ];
            //
            return $response;
         } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
         } 

    }

}
