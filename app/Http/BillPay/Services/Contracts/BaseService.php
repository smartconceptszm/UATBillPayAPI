<?php

namespace App\Http\BillPay\Services\Contracts;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Http\BillPay\Services\Contracts\IFindOneByService;
use App\Http\BillPay\Services\Contracts\IFindByIdService;
use App\Http\BillPay\Services\Contracts\IFindAllService;
use App\Http\BillPay\Services\Contracts\ICreateService;
use App\Http\BillPay\Services\Contracts\IUpdateService;
use App\Http\BillPay\Services\Contracts\IDeleteService;
use Exception;

abstract class BaseService implements ICreateService, IFindAllService,
                                IFindByIdService, IFindOneByService,
                                IUpdateService, IDeleteService
{
    
   protected $repository;
   public function __construct(BaseRepository $repository)
   {
      $this->repository=$repository;
   }

   public function findAll(array $criteria = null):array|null
   {

      try {
         return $this->repository->findAll();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

   public function findById(string $id):object|null
   {

      try {
         return  $this->repository->findById($id);
      } catch (\Exception $e) {
         throw new Exception($e->getMessage());
      }

   }

   public function findOneBy(array $criteria):object|null
   {

      try {
         return $this->repository->findOneBy($criteria);
      } catch (\Exception $e) {
         throw new Exception($e->getMessage());
      }

   }
   
   public function create(array $data):object|null
   {

      try {
         return $this->repository->create($data);
      } catch (\Exception $e) {
         throw new Exception($e->getMessage());
      }

   }

   public function update(array $data, string $id):object|null
   {

      try {
         return $this->repository->update($data,$id);
      } catch (\Exception $e) {
         throw new Exception($e->getMessage());
      }

   }

   public function delete(string $id):bool
   {
      
      try {
         return $this->repository->delete($id);
      } catch (\Exception $e) {
         throw new Exception($e->getMessage());
      }

   }

}
