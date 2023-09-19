<?php

namespace App\Http\Services\USSD;

use App\Http\Services\Contracts\EfectivoPipelineContract;

use App\Http\Services\USSD\ErrorResponses\IErrorResponse;
use App\Http\DTOs\BaseDTO;

class Step_HandleErrorResponse extends EfectivoPipelineContract
{
    
	public function __construct(
		private IErrorResponse $errorMenu)
	{}
	
	protected function stepProcess(BaseDTO $txDTO)
	{

		try {
			if($txDTO->error){
					$txDTO = $this->errorMenu->handle($txDTO);   
			}
		} catch (\Throwable $e) {
			$txDTO->error='At handle error response menu'.$e->getMessage();
		}
		return $txDTO;

	}


}