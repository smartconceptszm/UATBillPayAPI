<?php

namespace App\Http\BillPay\Repositories\Contracts;

interface IFindAllRepository
{
   public function findAll(array $criteria = null, array $fields = ['*']):array|null;
}