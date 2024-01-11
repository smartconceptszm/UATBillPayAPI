<?php
namespace App\Http\ScheduledTasks;

use App\Http\Services\Payments\PaymentFailedService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RetryFailedTrasactions 
{

	public function __construct(
		private PaymentFailedService $failedPaymentService) 	
	{}

	public function __invoke()
	{

		try {
			Log::info("Scheduled task (RetryFailedTrasactions) invoked");
			$theDate = Carbon::yesterday();
			$theYear = $theDate->year;
			$theMonth = \strlen((string)$theDate->month)==2?$theDate->month:"0".(string)$theDate->month;
			$theDay = \strlen((string)$theDate->day)==2?$theDate->day:"0".(string)$theDate->day;
			$from = $theYear . '-' . $theMonth . '-' .$theDay. ' 00:00:00';
			$to = $theYear . '-' . $theMonth . '-' .$theDay. ' 23:59:59';
			$query = DB::table('payments')
					->select('id', 'error')
					->whereIn('paymentStatus', ['SUBMITTED','SUBMISSION FAILED','PAYMENT FAILED'])
					->whereDate('created_at', '>=', $from)
					->whereDate('created_at', '<=', $to)
					->get();
			$providerErrors = $query->filter(
						function ($item) {
							if ( $item->paymentStatus == 'SUBMITTED'
									|| (\strpos($item->error,"Status Code"))
									|| (\strpos($item->error,"Status code"))
									|| (\strpos($item->error,"on get transaction status"))
									|| (\strpos($item->error,"Get Token error"))
									|| (\strpos($item->error,"on collect funds"))) 
							{
									return $item;
							}
						}
					)->values();

			$transactions = \json_decode($providerErrors, true);
			foreach ($transactions as $transaction) {
				$this->failedPaymentService->update($transaction['id']);
			}
		} catch (\Exception $e) {
			Log::error("In RetryFailedTrasactions, scheduled task: " . $e->getMessage());
		}

	}

}
