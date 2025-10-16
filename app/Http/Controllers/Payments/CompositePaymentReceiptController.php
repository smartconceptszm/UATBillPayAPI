<?php

namespace App\Http\Controllers\Payments;

use App\Http\Services\Payments\CompositePaymentReceiptFailedService;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Http\Services\Payments\PaymentService;
use App\Jobs\CompositePaymentReceiptJob;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class CompositePaymentReceiptController extends Controller
{


   private $validationRules = [
      'allocations' => 'required|array',
      'transaction_id' => 'required',
      'urlPrefix' => 'required',
      'client_id' => 'required',
      'menu_id' => 'required'
   ];


   public function __construct(
		private CompositePaymentReceiptFailedService $compositePaymentReceiptFailedService)
	{}

   public function create(Request $request, PaymentService $paymentService)
   {

      try {

         //Dispatch Receipting Jobs
            $data = $this->validate($request, $this->validationRules);
            foreach ($data['allocations'] as $allocation) {
               $jobData = [
                  'customerAccount' => $allocation['customerAccount'],
                  'receiptAmount' => $allocation['allocatedAmount'],
                  'transaction_id' => $data['transaction_id'],
                  'urlPrefix' => $data['urlPrefix'],
                  'client_id' => $data['client_id'],
                  'menu_id' => $data['menu_id']
               ];

               CompositePaymentReceiptJob::dispatch($jobData)
                                          ->delay(Carbon::now()->addSeconds(1))
                                          ->onQueue('UATlow');
            }
         //

         //Update Main Payment Transaction Status
            $paymentService->update(['paymentStatus'=>PaymentStatusEnum::Receipt_Delivered->value],$data['transaction_id']);
         //

         $this->response['data'] = ['Message' => "Receipting process initiated"];
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

   /**
    * Update the specified resource in storage.
   */
   public function update(Request $request, string $id)
   {

      try {
         $this->response['data'] = $this->compositePaymentReceiptFailedService->update($id);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

}
