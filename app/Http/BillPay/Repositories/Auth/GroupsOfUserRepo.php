<?php

namespace App\Http\BillPay\Repositories\Auth;

use App\Http\BillPay\Repositories\Contracts\IFindAllRepository;
use Illuminate\Support\Facades\DB;
use Exception;

class GroupsOfUserRepo implements IFindAllRepository
{
    public function __construct()
    {
    }

    public function findAll(array $criteria = null, array $fields = ['*']):array|null
    {

        try {
            $dto = (object)$criteria;
            $records = DB::table('users as u')
                        ->join('user_groups as ug','u.id','=','ug.user_id')
                        ->join('groups as g','g.id','=','ug.group_id')
                        ->where(['u.id', '=', $dto->user_id])
                        ->select('ug.id','ug.user_id','ug.group_id','g.name','g.description')
                        ->get();
            return $records->all();
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        } 

    }

}
