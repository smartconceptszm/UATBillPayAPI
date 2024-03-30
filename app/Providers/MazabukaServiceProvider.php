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

		//Menu Bus levy
			$this->app->singleton('PayBusLevy', function () {
				return $this->app->make(\App\Http\Services\USSD\Menus\PayBusLevy::class);
			});
			//Menu Step Handlers
				$this->app->singleton('PayBusLevy_Step_1', function () {
					return $this->app->make(\App\Http\Services\USSD\PayBusLevy\PayBusLevy_Step_1::class);
				});
				$this->app->singleton('PayBusLevy_Step_2', function () {
					return $this->app->make(\App\Http\Services\USSD\PayBusLevy\PayBusLevy_Step_2::class);
				});
				$this->app->singleton('PayBusLevy_Step_3', function () {
					return $this->app->make(\App\Http\Services\USSD\PayBusLevy\PayBusLevy_Step_3::class);
				});
				$this->app->singleton('PayBusLevy_Step_4', function () {
					return $this->app->make(\App\Http\Services\USSD\PayBusLevy\PayBusLevy_Step_4::class);
				});
				$this->app->singleton('PayBusLevy_Step_5', function () {
					return $this->app->make(\App\Http\Services\USSD\PayBusLevy\PayBusLevy_Step_5::class);
				});
			//
		//

		//Menu Market levy
			$this->app->singleton('PayMarketLevy', function () {
				return $this->app->make(\App\Http\Services\USSD\Menus\PayMarketLevy::class);
			});
			//Menu Step Handlers
				$this->app->singleton('PayMarketLevy_Step_1', function () {
					return $this->app->make(\App\Http\Services\USSD\PayMarketLevy\PayMarketLevy_Step_1::class);
				});
				$this->app->singleton('PayMarketLevy_Step_2', function () {
					return $this->app->make(\App\Http\Services\USSD\PayMarketLevy\PayMarketLevy_Step_2::class);
				});
				$this->app->singleton('PayMarketLevy_Step_3', function () {
					return $this->app->make(\App\Http\Services\USSD\PayMarketLevy\PayMarketLevy_Step_3::class);
				});
				$this->app->singleton('PayMarketLevy_Step_4', function () {
					return $this->app->make(\App\Http\Services\USSD\PayMarketLevy\PayMarketLevy_Step_4::class);
				});
				$this->app->singleton('PayMarketLevy_Step_5', function () {
					return $this->app->make(\App\Http\Services\USSD\PayMarketLevy\PayMarketLevy_Step_5::class);
				});
			//
		//

		//Menu Pay Property rates
			$this->app->singleton('PayPropertyRates', function () {
				return $this->app->make(\App\Http\Services\USSD\Menus\PayPropertyRates::class);
			});
			//Menu Step Handlers
				$this->app->singleton('PayPropertyRates_Step_1', function () {
					return $this->app->make(\App\Http\Services\USSD\PayPropertyRates\PayPropertyRates_Step_1::class);
				});
				$this->app->singleton('PayPropertyRates_Step_2', function () {
					return $this->app->make(\App\Http\Services\USSD\PayPropertyRates\PayPropertyRates_Step_2::class);
				});
				$this->app->singleton('PayPropertyRates_Step_3', function () {
					return $this->app->make(\App\Http\Services\USSD\PayPropertyRates\PayPropertyRates_Step_3::class);
				});
				$this->app->singleton('PayPropertyRates_Step_4', function () {
					return $this->app->make(\App\Http\Services\USSD\PayPropertyRates\PayPropertyRates_Step_4::class);
				});
				$this->app->singleton('PayPropertyRates_Step_5', function () {
					return $this->app->make(\App\Http\Services\USSD\PayPropertyRates\PayPropertyRates_Step_5::class);
				});
			//
		//

		//Billing Clients			
			$this->app->singleton('mazabuka', function () {
				return $this->app->make(\App\Http\Services\External\BillingClients\Mazabuka::class);
			});
			
         $this->app->singleton('mazabukaPOST-PAIDEnquiry', function () {
            return $this->app->make(\App\Http\Services\ExternalAdaptors\BillingEnquiryHandlers\MazabukaEnquiry::class);
         });

			// $this->app->singleton('PostPaymentMazabuka', function () {
			// 	return $this->app->make(\App\Http\Services\ExternalAdaptors\ReceiptingHandlers\ReceiptPaymentSwasco::class);
			// });
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
