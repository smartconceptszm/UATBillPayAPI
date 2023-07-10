<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\SMSMessageBulkService;
use App\Http\Controllers\Controller;

class SMSBulkController extends Controller
{

    private $validationRules=[
        'mobileNumbers' => 'required|string',
        'description' => 'required|string',
        'client_id' => 'required|string',
        'message' => 'required|string'
    ];



    protected $theService;
    public function __construct(SMSMessageBulkService $theService)
    {
        $this->theService = $theService;
    }
                    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request  $request){
        try {
            $this->response['data']=$this->theService->findAll($request->all());
        } catch (\Throwable $e) {
            $this->response['status']['code']=500;
            $this->response['status']['message']=$e->getMessage();
        }
        return response()->json( $this->response);
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
                    
    /**
     * Display the specified resource.
     *@param  \Illuminate\Http\Request  $request
     * @param  id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {

        try {
            $this->response['data']=$this->theService->findById($id);
        } catch (\Exception $e) {
            $this->response['status']['code']=500;
            $this->response['status']['message']=$e->getMessage();
        }
        return response()->json($this->response);

    }
                
                    
}
