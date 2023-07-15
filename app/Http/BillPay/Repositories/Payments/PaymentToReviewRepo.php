<?php

namespace App\Http\BillPay\Repositories\Payments;

use App\Http\BillPay\Repositories\Contracts\IFindByIdRepository;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentToReviewRepo implements IFindByIdRepository
{

    public function findById(string $id, array $fields = ['*']):object|null{

        try {
            return DB::table('payments as p')
                        ->join('sessions as s','p.session_id','=','s.id')
                        ->join('mnos as m','s.mno_id','=','m.id')
                        ->join('clients as c','s.client_id','=','c.id')
                        ->select('p.*','s.menu','s.sessionId','s.customerJourney',
                                    'c.code as clientCode','s.urlPrefix',
                                            'm.name as mnoName')
                        ->where('p.id', '=', $id)
                        ->first();
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        } 

    }

}
