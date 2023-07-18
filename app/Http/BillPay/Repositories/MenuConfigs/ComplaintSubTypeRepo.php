<?php

namespace App\Http\BillPay\Repositories\MenuConfigs;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\ComplaintSubType;
use Exception;

class ComplaintSubTypeRepo extends BaseRepository
{
    
   public function __construct(ComplaintSubType $model)
   {
      parent::__construct($model);
   }

   public function findAll(array $criteria = null, array $fields = ['*']):array|null
   {

      try {
         $records = $this->model->select($fields)
                           ->where($criteria)
                           ->orderBy('complaint_type_id')
                           ->orderBy('order')
                           ->get();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $records->all();

   }

}