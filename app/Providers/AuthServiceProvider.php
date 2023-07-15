<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Http\BillPay\Repositories\Auth\UserLoginProviderRepo;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
              // add custom guard provider 
      Auth::provider('UserLoginProviderRepo', function ($app, array $config) {
        return new UserLoginProviderRepo($app['hash'],$config['table']);
     });
    }
}
