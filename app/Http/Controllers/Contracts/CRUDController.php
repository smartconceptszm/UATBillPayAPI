<?php

namespace App\Http\Controllers\Contracts;

use App\Http\BillPay\Services\Contracts\BaseService;
use App\Http\Controllers\Contracts\Controller;
use Illuminate\Http\Request;

class CRUDController extends Controller
{

   protected $theService;
   public function __construct(BaseService $theService)
   {
      $this->theService = $theService;
   }

   /**
    * Display a listing of the resource.
    */
   public function index(Request $request)
   {

      try {
         $params = $request->all();
         $arrFields = ['*'];
         if(\Arr::exists($params, 'fieldList')){
            $arrFields = explode(',', $params['fieldList']);
            \unset($params['fieldList']);
         }
         $this->response['data'] = $this->theService->findAll($params,$arrFields);
      } catch (\Throwable $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

   /**
    * Store a newly created resource in storage.
    */
   public function store(Request $request)
   {

      try {
         //validate incoming request 
         $this->validate($request, $this->validationRules);
         $this->response['data'] = $this->theService->create($request->all());
      } catch (\Exception $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

   /**
    * Display the specified resource.
    */
   public function show(Request $request, string $id)
   {

      try {
         $params = $request->all();
         $arrFields = ['*'];
         if(\Arr::exists($params, 'fieldList')){
            $arrFields = explode(',', $params['fieldList']); 
         }
         $this->response['data'] = $this->theService->findById($id,$arrFields);
      } catch (\Exception $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

   /**
    * Display one resource.
    */
   public function findOneBy(Request $request)
   {

      try {
         $params = $request->all();
         $arrFields = ['*'];
         if(\Arr::exists($params, 'fieldList')){
            $arrFields = explode(',', $params['fieldList']);
            \unset($params['fieldList']);
         }
         $this->response['data'] = $this->theService->findOneBy($params,$arrFields);
      } catch (\Exception $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

   /**
    * Update the specified resource in storage.
    */
   public function update(Request $request, string $id)
   {

      try {
            $this->response['data'] = $this->theService->update($request->all(),$id);
      } catch (\Exception $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

   /**
    * Remove the specified resource from storage.
    */
   public function destroy(string $id)
   {
      
      try {
         $this->response['data'] = $this->theService->delete($id);
      } catch (\Exception $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }
}
