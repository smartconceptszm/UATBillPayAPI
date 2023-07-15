<?php

namespace App\Http\BillPay\Services\Contracts;

interface IFindByIdService
{
   public function findById(string $id, array $fields = ['*']):object|null;
}
