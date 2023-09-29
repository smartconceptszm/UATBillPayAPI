<?php

namespace App\Http\Controllers\USSD;

use App\Http\Services\Clients\MnoService;
use App\Http\Services\USSD\USSDService;
use App\Http\Controllers\Controller;
use App\Http\DTOs\UssdDTO;
use Illuminate\Http\Request;

class USSDController extends Controller
{

	public function __construct(
		protected USSDService $theService,
		protected MnoService $mnoService, 
		protected UssdDTO $theDTO)
	{}

	protected function getUrlPrefix(Request $request):string
	{
		$requestUrlArr = \explode("/",$request->url());
		$clientUrlPrefix = $requestUrlArr[\count($requestUrlArr)-2];
		return $clientUrlPrefix;
	}

	protected function getMNO(string $mnoName)
	{

		$mno = $this->mnoService->findOneBy(['name'=>$mnoName]);               
		return $mno->id;

	}

	protected function responder(Request $request)
	{
		//For Terminate Middleware
		$request->merge(['ussdParams' =>$this->theDTO->toArray()]);
		//Respond
		$theHeaders = $this->prepHeaders($this->theDTO);
		return response($this->theDTO->response,200)->withHeaders($theHeaders);
	}

	protected function prepHeaders(UssdDTO $txDTO): array
	{

		$theHeaders = ['Content-Type" => "text/plain'];
		if ($txDTO->lastResponse) {
			$theHeaders['Freeflow'] = "FB";
		} else {
			$theHeaders['Freeflow'] = "FC";
		}

		if ($txDTO->clean == "clean-session") {
			$theHeaders['Expires'] = "-1";
			$theHeaders['Pragma'] = "no-cache";
			$theHeaders['Cache-Control'] = "max-age=0";
		}
		return $theHeaders;
		
	}
    
}
