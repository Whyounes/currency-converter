<?php

namespace YouCan\CurrencyConverter\Tests\Datasources\CurrencyConverterAPI;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use YouCan\CurrencyConverter\Datasources\CurrencyConverterAPI\Converter;

class ConverterTest extends TestCase
{
    /** @var \YouCan\CurrencyConverter\Datasources\FixerIO\Converter */
    protected $converter;

    public function testThrowsExceptionDoesntSupportFeature()
    {
        $this->expectException(\Exception::class);
        $this->converter->latestRates('');
    }

    public function testThrowsErrorIfAPIKeyNotSet()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->converter = new Converter(new Client());

        $this->converter->convert('USD', 'EUR', 10.0);
    }

    public function testThrowsExceptionIfCurrencyIsWrong()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->converter = new Converter(new Client());

        $this->converter->convert('WRONG', 'EUR', 10.0);
    }

    public function testThrowsErrorIfAccessKeyIsWrong()
    {
        $this->expectException(\Exception::class);

        $this->converter = new Converter(new Client(),'wrong');

        $this->converter->latestRates('');
    }

    protected function setUp()
    {
        parent::setUp();

        $this->converter = new Converter(new Client());
        $this->converter->setApikey($_ENV['CONVERTER_API_KEY']);
    }
}
