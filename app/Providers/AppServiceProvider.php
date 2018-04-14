<?php

namespace App\Providers;

use Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //扩展自定义电话规则
        Validator::extend('phone', function($attribute, $value, $parameters, $validator) {
            return preg_match("/^1[34578][0-9]{9}$/", $value) || preg_match("/^((0[0-9]{2,3})-)([0-9]{7,8})$/", $value);
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
