<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\SMSMessageBulkCustomService;
use App\Http\Controllers\Controller;

class SMSBulkCustomController extends Controller
{

    private $validationRules=[
        'description' => 'required|string',
        'client_id' => 'required|string',
        'messages' => 'required|string'
    ];

    protected $theService;
    public function __construct(SMSMessageBulkCustomService $theService)
    {
        $this->theService = $theService;
    }
                                  
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request  $request)
    {

        try {
            //validate incoming request 
            $this->validate($request, $this->validationRules);
            $this->response['data']=$this->theService->create($request->all());
        } catch (\Exception $e) {
            $this->response['status']['code']=500;
            $this->response['status']['message']=$e->getMessage();
        }
        return response()->json($this->response);

    }
      

}
