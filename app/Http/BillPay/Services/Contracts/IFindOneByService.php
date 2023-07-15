<?php

namespace App\Http\BillPay\Services\Contracts;

interface IFindOneByService
{
   public function findOneBy(array $criteria, array $fields = ['*']):object|null;
}
