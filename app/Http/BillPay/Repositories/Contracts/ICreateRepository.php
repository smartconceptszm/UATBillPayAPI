<?php

namespace App\Http\BillPay\Repositories\Contracts;

interface ICreateRepository
{
   public function create(array $data):object|null;
}