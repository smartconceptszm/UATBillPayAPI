<?php

namespace App\Http\BillPay\Repositories;

use App\Http\BillPay\Repositories\Contracts\IFindAllRepository;
use Illuminate\Support\Facades\DB;
use Exception;

class UserOfClientRepo implements IFindAllRepository
{

    public function findAll(array $criteria = null):array|null
    {

        try {
            $dto=(object)$criteria;
            $records = DB::table('users')
                        ->select('*')
                        ->where('client_id', '=', $dto->client_id)
                        ->get();
            return $records->all();
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        } 

    }

}
