<?php

namespace App\Http\BillPay\Repositories\MenuConfigs;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\ComplaintType;
use Exception;

class ComplaintTypeRepo extends BaseRepository
{
    
   public function __construct(ComplaintType $model)
   {
      parent::__construct($model);
   }

   public function findAll(array $criteria = null, array $fields = ['*']):array|null
   {

      try {
         $records = $this->model->select($fields)
                           ->where($criteria)->orderBy('order')->get();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $records->all();

   }

}