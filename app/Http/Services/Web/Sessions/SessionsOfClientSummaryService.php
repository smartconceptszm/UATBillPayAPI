<?php

namespace App\Http\Services\Web\Sessions;

use Illuminate\Support\Facades\DB;
use Exception;

class SessionsOfClientSummaryService
{

	public function findAll(array $criteria):array|null{

		try {
			$dto=(object)$criteria;
			$dto->dateFrom = $dto->dateFrom." 00:00:00";
         $dto->dateTo = $dto->dateTo." 23:59:59";
			$records = DB::table('sessions as s')
						->join('mnos as m','m.id','=','s.mno_id')
						->select(DB::raw('count(s.id) as requestsCount, 
											s.menu as service,m.name as mno'))
						->where('s.client_id', '=', $dto->client_id);
			if($dto->dateFrom && $dto->dateTo){
					$records =$records->whereBetween('s.created_at', [$dto->dateFrom, $dto->dateTo]);
			}
			$records = $records->groupBy('service', 'mno')
										->orderBy('mno', 'asc')
										->get();
			return $records->all();
		} catch (\Throwable $e) {
			throw new Exception($e->getMessage());
		} 

	}

}
