<?php

namespace App\Http\BillPay\Services\Contracts;
use App\Http\BillPay\DTOs\BaseDTO;
use Closure;

abstract class EfectivoPipelineContract
{

    public function handle(BaseDTO $txDTO, Closure $next)
    {
        return $next($this->stepProcess($txDTO));
    }
    protected abstract function stepProcess(BaseDTO $txDTO);

}