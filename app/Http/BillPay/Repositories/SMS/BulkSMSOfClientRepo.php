<?php

namespace App\Http\BillPay\Repositories\SMS;

use App\Http\BillPay\Repositories\Contracts\IFindAllRepository;

class BulkSMSOfClientRepo implements IFindAllRepository
{
    private $table = 'bulk_messages';

    public function findAll(array $criteria = null, array $fields = ['*']):array|null
    {

        try {
            $dto = (object)$criteria;
            $records = DB::table($this->table.' as m')
                    ->select('*')
                    ->where('m.client_id', '=', $dto->client_id);
            if($dto->from && $dto->to){
                $records =$records->whereDate('m.created_at', '>=', $dto->from)
                                    ->whereDate('m.created_at', '<=', $dto->to);
            }
            $records =$records->get();
            return $records->all();
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        } 

    }
    
}
