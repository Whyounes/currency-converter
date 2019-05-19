<?php

namespace YouCan\CurrencyConverter;

interface Converter
{
    /**
     * @param string $baseCurrency
     * @param array $toCurrencies
     *
     * @return array
     */
    public function latestRates(string $baseCurrency, array $toCurrencies = []): array;

    /**
     * @param string $from
     * @param string $to
     * @param float $amount
     *
     * @return float
     */
    public function convert(string $from, string $to, float $amount): float;
}
