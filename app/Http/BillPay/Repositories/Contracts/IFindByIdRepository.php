<?php

namespace App\Http\BillPay\Repositories\Contracts;

interface IFindByIdRepository
{
   public function findById(string $id, array $fields = ['*']):object|null;
}