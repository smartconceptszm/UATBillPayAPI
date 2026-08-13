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
			$this->app->bind('mazabukaLocalCommonAccount', function () {
				return $this->app->make(\App\Http\Services\External\BillingClients\MazabukaLocal::class);
			});

			$this->app->bind('mazabukaRemoteCommonAccount', function () {
				return $this->app->make(\App\Http\Services\External\BillingClients\MazabukaOnCommonAccount::class);
			});

			$this->app->bind('mazabukaRemoteCustomerAccount', function () {
				return $this->app->make(\App\Http\Services\External\BillingClients\MazabukaOnCustomerAccount::class);
			});

			$this->app->bind('ReceiptMazabukaOnCommonAccount', function () {
				return $this->app->make(\App\Http\Services\Gateway\ReceiptingHandlers\ReceiptMazabukaOnCommonAccount::class);
			});

			$this->app->bind('ReceiptMazabukaOnCustomerAccount', function () {
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
