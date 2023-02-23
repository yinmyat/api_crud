<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Address\CustomerRepository;
use App\Repositories\Address\CustomerRepositoryInterface;
use App\Repositories\Admin\AdminRepository;
use App\Repositories\Admin\AdminRepositoryInterface;


class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(CustomerRepositoryInterface::class, CustomerRepository::class);
        $this->app->singleton(AdminRepositoryInterface::class, AdminRepository::class);

    }
}
