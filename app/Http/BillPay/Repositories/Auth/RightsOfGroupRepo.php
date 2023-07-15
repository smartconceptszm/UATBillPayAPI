<?php

namespace App\Http\BillPay\Repositories\Auth;

use App\Http\BillPay\Repositories\Contracts\IFindAllRepository;
use Illuminate\Support\Facades\DB;
use Exception;

class RightsOfGroupRepo implements IFindAllRepository
{

    public function findAll(array $criteria = null, array $fields = ['*']):array|null
    {

        try {
            $dto=(object)$criteria;
            $records=DB::table('group_rights as gr')
                        ->join('rights as r','r.id','=','gr.right_id')
                        ->where([['gr.group_id', '=', $dto->group_id]])
                        ->select('gr.id','gr.right_id','gr.group_id','r.name','r.description')
                        ->get();
            return $records->all();
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        } 

    }

}
