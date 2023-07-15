<?php

namespace App\Http\BillPay\Repositories\Contracts;

interface IFindOneByRepository
{
   public function findOneBy(array $criteria, array $fields = ['*']):object|null;
}