<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\MessageService;
use App\Http\Controllers\Controller;

class MessageController extends Controller
{

    protected $theService;
    public function __construct(MessageService $theService)
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
