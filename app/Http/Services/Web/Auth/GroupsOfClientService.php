<?php

namespace App\Http\Services\Web\Auth;

use Illuminate\Support\Facades\DB;
use Exception;

class GroupsOfClientService 
{

   public function findAll(array $criteria):array|null{

      try {
         $dto = (object)$criteria;
			$records = DB::table('groups')
							->where('client_id', '=', $dto->client_id)
							->select('*')
							->get();
			return $records->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

}