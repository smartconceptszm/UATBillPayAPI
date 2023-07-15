<?php

namespace App\Http\BillPay\Repositories\Contracts;

interface IRepository
{

   public function findAll(array $criteria = null, array $fields = ['*']):array|null;

   public function create(array $data):object|null;

   public function findById(string $id, array $fields  = ['*']):object|null;

   public function findOneBy(array $criteria, array $fields  = ['*']):object|null;

   public function update(array $data, string $id):object|null;

   public function delete(string $id):bool;
  
}