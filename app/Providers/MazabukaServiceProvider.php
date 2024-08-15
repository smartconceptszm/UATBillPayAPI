<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MazabukaServiceProvider extends ServiceProvider 
{

	/**
	 * Register services.
	 */
	public function register(): void
	{

		//Billing Clients			
			$this->app->singleton('mazabuka', function () {
				return $this->app->make(\App\Http\Services\External\BillingClients\Mazabuka::class);
			});
			
			$this->app->singleton('ReceiptPostPaidMazabuka', function () {
				return $this->app->make(\App\Http\Services\External\Adaptors\ReceiptingHandlers\ReceiptPaymentMazabuka::class);
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
