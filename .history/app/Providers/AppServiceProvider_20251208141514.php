<?php

namespace App\Providers;

use App\Models\Tenant;
use App\Observers\CompanyObserver;
use Illuminate\Support\ServiceProvider;
use App\Models\HomepageContent;


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
        // Navbar ve Footer verilerini TÜM VIEW'LARA share et
        View::composer('*', function ($view) {
            // Navbar Content
            $navbarContent = HomepageContent::getSection('navbar_content');
            
            // Footer Content  
            $footerContent = HomepageContent::getSection('footer_content');
            
            // View'a gönder
            $view->with([
                'navbarContent' => $navbarContent,
                'footerContent' => $footerContent
            ]);
        });
    }
}
