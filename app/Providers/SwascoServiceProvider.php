<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SwascoServiceProvider extends ServiceProvider
{

	/**
	 * Register services.
	 */
	public function register(): void
	{

		//Update Handlers
			$this->app->bind('UpdateDetails_swasco', function () {
				return $this->app->make(\App\Http\Services\USSD\UpdateDetails\ClientCallers\UpdateDetails_Swasco::class);
			});
		//

		//Survey Entry Handlers
			$this->app->bind('Survey_swasco', function () {
				return $this->app->make(\App\Http\Services\USSD\Survey\ClientCallers\Survey_Local::class);
			});
		//

		//Billing Client
			$this->app->bind('SwascoPostPaid', function () {
            return $this->app->make(\App\Http\Services\External\BillingClients\SwascoPostPaid::class);
         });
		//
		
		//Receipting Handlers
			$this->app->bind('ReceiptPostPaidSwasco', function () {
				return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptPostPaidSwasco::class);
			});

			$this->app->bind('ReceiptReconnectionSwasco', function () {
				return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptReconnectionSwasco::class);
			});
			
			$this->app->bind('ReceiptVacuumTankerSwasco', function () {
				return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptVacuumTankerSwasco::class);
			});
		//

		//Complaint Handlers
			$this->app->bind('Complaint_swasco', function () {
					return $this->app->make(\App\Http\Services\USSD\FaultsComplaints\ClientCallers\Complaint_Swasco::class);
			});
		//
  
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
