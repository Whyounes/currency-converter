<?php

namespace YouCan\CurrencyConverter\Tests\Datasources\FixerIO;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use YouCan\CurrencyConverter\Datasources\FixerIO\Converter;

class ConverterTest extends TestCase
{
    /** @var \YouCan\CurrencyConverter\Datasources\FixerIO\Converter */
    protected $fixerConverter;

    public function testCanGetLatestRates()
    {
        // No base will fallback to `EUR` on free plans
        $latestRates = $this->fixerConverter->latestRates('');

        $this->assertIsArray($latestRates);
        $this->assertArrayHasKey('success', $latestRates);
        $this->assertArrayHasKey('rates', $latestRates);
        $this->assertInstanceOf(\stdClass::class, $latestRates['rates']);
        $this->assertTrue($latestRates['success']);
    }

    public function testThrowsErrorIfAccessKeyNotSet()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->fixerConverter = new Converter(new Client());

        $this->fixerConverter->latestRates('');
    }

    public function testThrowsErrorIfAccessKeyIsWrong()
    {
        $this->expectException(\Exception::class);

        $this->fixerConverter = new Converter(new Client(),'wrong');

        $this->fixerConverter->latestRates('');
    }

    protected function setUp()
    {
        parent::setUp();

        $this->fixerConverter = new Converter(new Client());
        $this->fixerConverter->setAccessKey($_ENV['FIXER_API_KEY']);
        $this->fixerConverter->freePlan();
    }
}
