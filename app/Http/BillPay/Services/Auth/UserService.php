<?php

namespace App\Http\BillPay\Services\Auth;

use App\Http\BillPay\Services\Contracts\BaseService;
use App\Http\BillPay\Repositories\Auth\UserRepo;
use Exception;

class UserService extends BaseService
{

   public function __construct(UserRepo $repository)
   {
      parent::__construct($repository);
   }

   public function create(array $data):object|null
   {

      try {
         $dto = (object)$data;
         $dto->password = app('hash')->make($dto->password);
         $response = $this->repository->create(\get_object_vars($dto));
      } catch (\Exception $e) {
         throw new Exception($e->getMessage());
      }
      return $response;

   }

   public function update(array $data, string $id):object|null
   {

      try {
         $dto = (object)$data;
         if($dto->password){
               $dto->password = app('hash')->make($dto->password);
         }
         return $this->repository->update(\get_object_vars($dto), $id);
      } catch (\Exception $e) {
         throw new Exception($e->getMessage());
      }

   }

}
