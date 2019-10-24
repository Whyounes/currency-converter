<?php

namespace YouCan\CurrencyConverter;

use Carbon\Carbon;
use Psr\Cache\CacheItemPoolInterface;

class CachingConverter implements Converter
{
    /** @var \YouCan\CurrencyConverter\Converter */
    protected $converter;

    /** @var \Psr\Cache\CacheItemInterface */
    protected $cache;

    /** @var string */
    protected $expiresAfter = "+1 day";

    public function __construct(Converter $converter, CacheItemPoolInterface $cache)
    {
        $this->converter = $converter;
        $this->cache = $cache;
    }

    /**
     * @param string $baseCurrency
     * @param array $toCurrencies
     *
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function latestRates(string $baseCurrency, array $toCurrencies = []): array
    {
        return $this->converter->latestRates($baseCurrency, $toCurrencies);
    }

    /**
     * @return int
     * @throws \InvalidArgumentException
     */
    protected function getExpireTime()
    {
        if (is_null($this->expiresAfter)) {
            return 0;
        }

        $time = strtotime($this->expiresAfter);

        throw_if($time === false, new \InvalidArgumentException('`expiresAfter` value cannot be parsed by `strotime`'));

        return $time;
    }

    /**
     * @param string $from
     * @param string $to
     * @param float $amount
     *
     * @return float
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function convert(string $from, string $to, float $amount): float
    {
        $cacheKey = sprintf(
            '%s-convert-%s-%s-%s',
            md5(get_class($this->converter)), $from, $to, $amount
        );

        $cacheItem = $this->cache->getItem($cacheKey);

        if (is_null($cacheItem->get())) {
            $cacheItem->set($this->converter->convert($from, $to, $amount));
            $cacheItem->expiresAfter($this->getExpireTime());
            $this->cache->save($cacheItem);
        }

        return (float)$cacheItem->get();
    }

    /**
     * @return string|null
     */
    public function getExpiresAfter(): ?string
    {
        return $this->expiresAfter;
    }

    /**
     * @param string $time a valid `strtotime` string. Or `null` to disable caching.
     */
    public function expiresAfter(string $time = null)
    {
        $this->expiresAfter = $time;
    }
}
