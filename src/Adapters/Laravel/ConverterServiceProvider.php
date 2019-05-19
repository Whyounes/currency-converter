<?php

namespace YouCan\CurrencyConverter\Adapters\Laravel;

use Illuminate\Support\ServiceProvider;
use Psr\Cache\CacheItemPoolInterface;
use YouCan\CurrencyConverter\CachingConverter;
use YouCan\CurrencyConverter\Converter;

class ConverterServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(CachingConverter::class, function () {
            return new CachingConverter(
                $this->app->make(Converter::class),
                $this->app->make(CacheItemPoolInterface::class)
            );
        });
    }

    public function boot()
    {
    }
}
