<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use App\Channel;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //配置日期中文显示
        Carbon::setlocale('zh');
        // 配置视图合成
        \View::composer('*',function ($view){
           $channels = \Cache::rememberForever('channels',function (){
                return Channel::all();
           }) ;
           $view->with('channels',$channels);
        });

        // 垃圾消息过滤
        \Validator::extend('spamfree','App\Rules\SpamFree@passes');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // 本地环境开启dubug
        if ($this->app->isLocal()) {
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
        }
    }
}
