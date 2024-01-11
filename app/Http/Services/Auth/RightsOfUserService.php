<?php

namespace App\Http\Services\Auth;

use Illuminate\Support\Facades\DB;
use Exception;

class RightsOfUserService
{

   public function findAll(array $criteria = null):array|null{
      try {
			$dto = (object)$criteria;
			$records = DB::table('users as u')
							->join('user_groups as ug','u.id','=','ug.user_id')
							->join('group_rights as gr','ug.group_id','=','gr.group_id')
							->join('rights as r','r.id','=','gr.right_id')
							->where([['u.id', '=', $dto->user_id]])
							->select('r.*')
							->get();
			return $records->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
         
      }
   }

}
