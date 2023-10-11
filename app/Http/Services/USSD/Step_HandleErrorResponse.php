<?php

namespace App\Http\Services\USSD;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;

class Step_HandleErrorResponse extends EfectivoPipelineContract
{
   
	
	protected function stepProcess(BaseDTO $txDTO)
	{

		try {
			if($txDTO->error){
				$errorHandler = App::make($txDTO->errorType);
            $txDTO = $errorHandler->handle($txDTO);
			}
		} catch (\Throwable $e) {
			$txDTO->error='At handle error response menu'.$e->getMessage();
		}
		return $txDTO;

	}


}