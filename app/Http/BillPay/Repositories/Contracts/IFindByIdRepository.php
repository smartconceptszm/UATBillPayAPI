<?php

namespace App\Http\BillPay\Repositories\Contracts;

interface IFindByIdRepository
{
   public function findById(string $id):object|null;
}