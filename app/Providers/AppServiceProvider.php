<?php

namespace App\Providers;

use App\Repositories\CategoryRepository;
use App\Repositories\CodePromoRepository;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\CodePromoRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CategoryRepositoryInterface::class, CategoryRepository::class);

        $this->app->singleton(ProductRepositoryInterface::class, ProductRepository::class);

        $this->app->singleton(OrderRepositoryInterface::class, OrderRepository::class);

        $this->app->singleton(CodePromoRepositoryInterface::class, CodePromoRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
