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
			$this->app->singleton('mazabukaLocalCommonAccount', function () {
				return $this->app->make(\App\Http\Services\External\BillingClients\MazabukaLocal::class);
			});

			$this->app->singleton('mazabukaRemoteCommonAccount', function () {
				return $this->app->make(\App\Http\Services\External\BillingClients\MazabukaOnCommonAccount::class);
			});

			$this->app->singleton('mazabukaRemoteCustomerAccount', function () {
				return $this->app->make(\App\Http\Services\External\BillingClients\MazabukaOnCustomerAccount::class);
			});

			$this->app->singleton('ReceiptMazabukaOnCommonAccount', function () {
				return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptMazabukaOnCommonAccount::class);
			});

			$this->app->singleton('ReceiptMazabukaOnCustomerAccount', function () {
				return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptMazabukaOnCustomerAccount::class);
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
