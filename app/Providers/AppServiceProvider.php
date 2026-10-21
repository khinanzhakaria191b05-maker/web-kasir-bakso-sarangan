<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Nama toko tersedia di semua view sebagai $shopName
        View::composer('*', function ($view) {
            $view->with('shopName', Setting::get('shop_name', 'Mie Ayam Bakso Sarangan'));
        });
    }
}
