# Introduction

This package allows for converting currencies using a third party API.


## Drivers

This is the list of current available drivers.

### Fixer.io

the `src/Datasources/FixerIO` datasource uses the [fixer.io](https://fixer.io) service, check the [https://fixer.io/documentation](docs) to get started, and set your `access_secret` key to start.


### Free Currency Converter

the `src/Datasources/CurrencyConverterAPI` datasource uses the [currencyconverterapi.com](https://www.currencyconverterapi.com) service, register to get an API key and check the [docs](https://www.currencyconverterapi.com/docs).

## Creating Your Own Datasources

Just implement the `Converter` interface and define the logic to retrieve the available rates for a given currency.

## Caching

The `CachingConverter` is responsible for getting data from a datasource and caching it for a given time (default: +1 day). Check the `tests` folder for usage.

## Integration

### Laravel

Follow these steps:

- Bind the `YouCan\CurrencyConverter\Converter` interface to a given drive (Ex: Fixer.io) in your container.
- If you use Laravel 5.5+ package autodiscovery, the  `YouCan\CurrencyConverter\Adapters\Laravel\ConverterServiceProvider` service provider will be auto registered. Otherwise, you need to add it to your providers list, and register the `CurrencyConverter` alias.

Now you can use the `CurrencyConverter` facade.