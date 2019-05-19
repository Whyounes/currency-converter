<?php

namespace YouCan\CurrencyConverter\Tests;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use YouCan\CurrencyConverter\CachingConverter;
use YouCan\CurrencyConverter\Datasources\CurrencyConverterAPI\Converter;

class CachingConverterTest extends TestCase
{
    /** @var \YouCan\CurrencyConverter\CachingConverter */
    protected $converter;

    /** @var Converter */
    protected $freeConverter;

    /** @var \Symfony\Component\Cache\Adapter\FilesystemAdapter */
    protected $cache;

    public function testCacheIsHit()
    {
        $from = 'USD';
        $to = 'EUR';
        $amount = 10.0;

        $cacheKey = sprintf(
            '%s-convert-%s-%s-%s',
            md5(get_class($this->freeConverter)), $from, $to, $amount
        );
        $this->cache->delete($cacheKey);

        $this->converter->convert($from, $to, $amount);

        $cacheItem = $this->cache->getItem($cacheKey);
        $this->assertNotNull($cacheItem);
        $this->assertNotNull($cacheItem->get());
    }

    protected function setUp()
    {
        parent::setUp();

        $this->freeConverter = new Converter(new Client());
        $this->freeConverter->setApikey($_ENV['CONVERTER_API_KEY']);

        $this->cache = new FilesystemAdapter(
            '',
            24 * 60 * 60,
            __DIR__ . '/storage/'
        );

        $this->converter = new CachingConverter($this->freeConverter, $this->cache);
    }

    protected function tearDown()
    {
        $this->cache->clear();

        parent::tearDown();

    }
}
