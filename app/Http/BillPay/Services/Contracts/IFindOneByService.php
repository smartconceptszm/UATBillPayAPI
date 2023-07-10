<?php

namespace App\Http\BillPay\Services\Contracts;

interface IFindOneByService
{
   public function findOneBy(array $data):object|null;
}
