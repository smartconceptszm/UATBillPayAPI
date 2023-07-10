<?php

namespace App\Http\BillPay\Services\Contracts;

interface IFindAllService
{
   public function findAll(array $criteria = null):array|null;
}
