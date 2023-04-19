<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Laravel\Services\CustomValidator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\Paginator;
use Schema;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();

        if(env('SECURE_ASSET',FALSE) == TRUE){
            $this->app['request']->server->set('HTTPS','on');
        }

        Validator::resolver(function($translator, $data, $rules, $messages)
        {
            return new CustomValidator($translator, $data, $rules, $messages);
        });
    }
}
