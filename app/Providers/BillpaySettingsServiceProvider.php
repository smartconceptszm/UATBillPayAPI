<?php

namespace App\Providers;

use App\Http\Services\Auth\BillpaySettingsService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

class BillpaySettingsServiceProvider extends ServiceProvider
{

	/**
	 * Register services.
	 */
	public function register(): void
	{}

	/**
	 * Bootstrap services.
	 */
	public function boot(BillpaySettingsService $billpaySettingsService): void
	{

		$billpaySettings = \json_decode(Cache::get('billpaySettings',\json_encode([])), true);
		if(!$billpaySettings){
			Cache::put('billpaySettings', \json_encode($billpaySettingsService->getAllSettings()),Carbon::now()->addHours(24));
		}

	}
}
