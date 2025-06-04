<?php

namespace App\Http\Controllers\Promotions;

use App\Http\Services\Promotions\PromotionEntryService;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Http\Services\SMS\MessageService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PromotionEntryController extends Controller
{

	public function __construct(
		private PromotionEntryService $promotionEntryService,
      private MessageService $messageService)
	{}

   /**
    * Display a listing of the resource.
   */
   public function index(Request $request)
   {

      try {
         $this->response['data'] =  $this->promotionEntryService->findAll($request->query());
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

   }

   /**
    * Display the specified resource.
      */
   public function show(Request $request, string $id)
   {

      try {
         $this->response['data'] = $this->promotionEntryService->findById($id);
      } catch (\Throwable $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

      /**
    * Display the specified resource.
      */
      public function entriesOfPromotion(Request $request, string $id)
      {
   
         try {
            $this->response['data'] = $this->promotionEntryService->findAll(['promotion_id'=>$id]);
         } catch (\Throwable $e) {
               $this->response['status']['code'] = 500;
               $this->response['status']['message'] = $e->getMessage();
         }
         return response()->json($this->response);
   
      }


   /* Display REDEEMED PROMOTIONS
   */
  public function redeemed(Request $request)
  {

     try {
        $this->response['data'] =  $this->promotionEntryService->findAll(['status' => 'REDEEMED']);
     } catch (\Throwable $e) {
           $this->response['status']['code'] = 500;
           $this->response['status']['message'] = $e->getMessage();
     }
     return response()->json( $this->response);

  }


     /**
    * Display REDEEMED PROMOTIONS
   */
  public function notRedeemed(Request $request)
  {

     try {
        $this->response['data'] =  $this->promotionEntryService->findAll(['status' => 'RECORDED']);
     } catch (\Throwable $e) {
           $this->response['status']['code'] = 500;
           $this->response['status']['message'] = $e->getMessage();
     }
     return response()->json( $this->response);

  }

   /**
    * Display one resource.
      */
   public function findOneBy(Request $request)
   {

      try {
         $this->response['data'] = $this->promotionEntryService->findOneBy($this->getParameters($request));
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

      /**
    * Update the specified resource in storage.
      */
      public function sendEntrySMS(Request $request, Authenticatable $user, string $id)
      {
   
         try {

            $promotionEntry = $this->promotionEntryService->findById($id);
            $promoSMS=[
                     'mobileNumber' => $promotionEntry->mobileNumber,
                     'message' => $promotionEntry->message,
                     'client_id' => $user->client_id,
                     'type' => "NOTIFICATION"
                  ];
            $this->response['data'] = $this->messageService->send($promoSMS);

         } catch (\Throwable $e) {
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
         $this->response['data'] = $this->promotionEntryService->update($this->getParameters($request),$id);
      } catch (\Throwable $e) {
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
         $this->response['data'] = $this->promotionEntryService->delete($id);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }
   
}
