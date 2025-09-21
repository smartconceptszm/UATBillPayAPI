<?php

namespace App\Http\Controllers\Promotions;

use App\Http\Services\Promotions\RaffleDrawEntryService;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Http\Services\SMS\MessageService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RaffleDrawEntryController extends Controller
{

	public function __construct(
		private RaffleDrawEntryService $raffleDrawEntryService,
      private MessageService $messageService
      )
	{}

   /**
    * Display a listing of the resource.
   */
   public function index(Request $request)
   {

      try {
         $this->response['data'] =  $this->raffleDrawEntryService->findAll($request->query());
      } catch (\Throwable $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }


   /**
    * Display the specified resource.
   */
  public function drawEntriesOfPromotion(Request $request)
  {

     try {
        $this->response['data'] = $this->raffleDrawEntryService->drawEntriesOfPromotion($request->query());
     } catch (\Throwable $e) {
           $this->response['status']['code'] = 500;
           $this->response['status']['message'] = $e->getMessage();
     }
     return response()->json($this->response);

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
         $this->response['data'] = $this->raffleDrawEntryService->findById($id);
      } catch (\Throwable $e) {
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
         $this->response['data'] = $this->raffleDrawEntryService->findOneBy($this->getParameters($request));
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
         $this->response['data'] = $this->raffleDrawEntryService->update($this->getParameters($request),$id);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

   public function drawrandom(Request $request)
   {

      try {

         $params = [
                     'promotion_id' => $request->input('promotion_id'),
                     'theMonth' => $request->input('theMonth'),
                     'from' => $request->input('from'),
                     'to' => $request->input('to')
                     ];
         $this->response['data'] =  $this->raffleDrawEntryService->drawRandom($params);

      } catch (\Throwable $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }

     return response()->json( $this->response);

   }

   public function sendEntrySMS(Request $request, Authenticatable $user, string $id)
   {

      try {

         $raffleDrawEntry = $this->raffleDrawEntryService->findById($id);
         $promoSMS=[
                     'mobileNumber' => $raffleDrawEntry->mobileNumber,
                     'message' => $raffleDrawEntry->winMessage,
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
    * Remove the specified resource from storage.
      */
   public function destroy(string $id)
   {

      try {
         $this->response['data'] = $this->raffleDrawEntryService->delete($id);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

}