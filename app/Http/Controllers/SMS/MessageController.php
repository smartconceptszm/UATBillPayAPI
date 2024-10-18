<?php

namespace App\Http\Controllers\SMS;

use App\Http\Services\SMS\MessageService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessageController extends Controller
{

   private $validationRules=[
            'mobileNumber' => 'required|string|size:12',
            'client_id' => 'required|string',
            'message' => 'required|string'
         ];
	public function __construct(
		private MessageService $messageService)
	{}
              
   public function store(Request  $request)
   {

      try {
         $this->validate($request, $this->validationRules);
         $this->response['data'] = $this->messageService->send($this->getParameters($request));
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

}
