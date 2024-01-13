<?php

namespace App\Http\Services\Contracts;
use App\Http\DTOs\BaseDTO;
use Closure;

abstract class EfectivoPipelineContract
{

    public function handle(BaseDTO $txDTO, Closure $next)
    {
        if($txDTO->exitPipeline){
            return $txDTO;
        }
        return $next($this->stepProcess($txDTO));
    }
    protected abstract function stepProcess(BaseDTO $txDTO);

}