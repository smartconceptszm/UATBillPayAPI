<?php

namespace App\Http\BillPay\Services\Contracts;

interface IService
{

   public function findAll(array $criteria = null):array|null;

   public function findById(string $id):object|null;

   public function findOneBy(array $data):object|null;

   public function create(array $data):object|null;

   public function update(array $data, string $id):object|null;

   public function delete(string $id):bool;

}
