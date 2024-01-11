<?php

namespace App\Http\Services\External\BillingClients\Chambeshi;

use App\Models\ChambeshiAccount;
use Exception;

class ChambeshiAccountService
{

   public function __construct(
         protected ChambeshiAccount $model
   ) {}

   public function findAll(array $criteria = null):array|null
   {
      try {
         return $this->model->where($criteria)->get()->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

   public function findById(string $id) : object|null {
      try {
         return $this->model->findOrFail($id);
      } catch (\Exception $e) {
         throw new Exception($e->getMessage());
      }
   }

   public function findOneBy(array $criteria) : object|null {
      try {
         return $this->model->where($criteria)->first();
      } catch (\Exception $e) {
         throw new Exception($e->getMessage());
      }
   }


}