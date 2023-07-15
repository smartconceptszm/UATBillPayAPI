<?php

namespace App\Http\BillPay\Repositories\Payments;

use App\Http\BillPay\Repositories\Contracts\IFindAllRepository;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentToReviewBatchRepo implements IFindAllRepository
{

    public function findAll(array $criteria = null, array $fields = ['*']):array|null
    {

        try {
            $dto=(object)$criteria;
            $records = DB::table('payments as p')
                    ->select('id', 'errorMessage')
                    ->whereIn('p.paymentStatus', ['SUBMISSION FAILED','PAYMENT FAILED'])
                    ->where('p.client_id', '=', $dto->client_id);
            if($dto->from && $dto->to){
                $records =$records->whereDate('p.created_at', '>=', $dto->from)
                                    ->whereDate('p.created_at', '<=', $dto->to);
            }
            $records =$records->get();
            return $records->all();
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        } 

    }

}
