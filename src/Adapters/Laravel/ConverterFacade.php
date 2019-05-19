<?php

namespace YouCan\CurrencyConverter\Adapters\Laravel;

use Illuminate\Support\Facades\Facade;
use YouCan\CurrencyConverter\CachingConverter;

/**
 * Class ConverterFacade
 * @package YouCan\CurrencyConverter\Adapters\Laravel
 *
 */
class ConverterFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return CachingConverter::class;
    }
}
