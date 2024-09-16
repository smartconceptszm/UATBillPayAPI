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
			$this->app->singleton('UpdateDetails_swasco', function () {
				return $this->app->make(\App\Http\Services\USSD\UpdateDetails\ClientCallers\UpdateDetails_Swasco::class);
			});
		//

		//Survey Entry Handlers
			$this->app->singleton('Survey_swasco', function () {
				return $this->app->make(\App\Http\Services\USSD\Survey\ClientCallers\Survey_Local::class);
			});
		//

		//Billing Client
			$this->app->singleton('swascoPostPaid', function () {
            return $this->app->make(\App\Http\Services\External\BillingClients\Swasco::class);
         });
		//
		
		//Receipting Handlers
			$this->app->singleton('ReceiptPostPaidSwasco', function () {
				return $this->app->make(\App\Http\Services\External\ReceiptingHandlers\ReceiptPostPaidSwasco::class);
			});

			$this->app->singleton('ReceiptReconnectionSwasco', function () {
				return $this->app->make(\App\Http\Services\External\ReceiptingHandlers\ReceiptReconnectionSwasco::class);
			});
			
			$this->app->singleton('ReceiptVacuumTankerSwasco', function () {
				return $this->app->make(\App\Http\Services\External\ReceiptingHandlers\ReceiptVacuumTankerSwasco::class);
			});
		//

		//Complaint Handlers
			$this->app->singleton('Complaint_swasco', function () {
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
