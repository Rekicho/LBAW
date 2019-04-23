<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('showRating', function($rating){
            return "<?php for(\$i=0; \$i<$rating; \$i++)
            echo (\"<i class='fas fa-star'></i>\");
        
            for(\$i=0; \$i< 5 - $rating; \$i++)
            echo (\"<i class='far fa-star'></i>\"); 
        ?>";
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
