<?php

namespace App\Http\Controllers\SMS;

use App\Http\Services\SMS\MessageService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessageController extends Controller
{

   private $validationRules=[
            'mobileNumber' => 'required|string|size:12',
            'message' => 'required|string'
         ];
	public function __construct(
		private MessageService $theService)
	{}
              
   public function store(Request  $request)
   {

      try {
         $this->validate($request, $this->validationRules);
         $this->response['data'] = $this->theService->send($request->all());
      } catch (\Exception $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

}
