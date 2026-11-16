<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\View\View as ViewInstance;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        RateLimiter::for('login', function (Request $request): Limit {
            $email = $request->input('email');
            $normalizedEmail = is_string($email) ? Str::lower(trim($email)) : '';
            $key = Str::transliterate($normalizedEmail.'|'.$request->ip());

            return Limit::perMinute(5)->by($key);
        });

        View::composer('layouts.partials.navigation', function (ViewInstance $view): void {
            $view->with(
                'navigationCategories',
                Category::query()
                    ->where('status', 1)
                    ->orderBy('name')
                    ->get(['id', 'name', 'slug'])
            );
        });

        View::composer('layouts.partials.header', function (ViewInstance $view): void {
            $cartItemCount = auth()->user()?->cart()
                ->withSum('items', 'quantity')
                ->value('items_sum_quantity') ?? 0;

            $view->with('cartItemCount', (int) $cartItemCount);
        });
    }
}
