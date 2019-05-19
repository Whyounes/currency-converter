<?php

namespace YouCan\CurrencyConverter\Datasources\CurrencyConverterAPI;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use YouCan\CurrencyConverter\Converter as IConverter;

class Converter implements IConverter
{
    const BASE_API_URL = "https://free.currconv.com/api/v7/";

    /** @var \GuzzleHttp\Client */
    protected $httpClient;
    protected $onFreePlan = false;
    /** @var string */
    private $apikey;

    public function __construct(Client $httpClient, string $apikey = null)
    {
        $this->httpClient = $httpClient;
        $this->apikey = $apikey;
    }

    /**
     * @param string $baseCurrency
     * @param array $toCurrencies
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function latestRates(string $baseCurrency, array $toCurrencies = []): array
    {
        throw new Exception('Currency Converter API does not support this feature. Check doc (https://www.currencyconverterapi.com/docs)');
    }

    /**
     * @param string $from
     * @param string $to
     * @param float $amount
     *
     * @return float
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable|\Exception If it can't get data right now.
     */
    public function convert(string $from, string $to, float $amount): float
    {
        $q = $from . "_" . $to;
        $response = (array)$this->request('convert', ['q' => $q, 'amount' => $amount]);

        if (isset($response[$q])) {
            $rate = $response[$q];
        } elseif (
            isset($response['results']) &&
            $response['results'] instanceof \stdClass &&
            property_exists($response['results'], $q) &&
            $response['results']->{$q} instanceof \stdClass &&
            property_exists($response['results']->{$q}, 'val')
        ) {
            $rate = $response['results']->{$q}->val;
        } else {
            throw new Exception("Couldn't get a result now");
        }

        return $amount * (float)$rate;
    }

    /**
     * @param string $endpoint
     * @param array $params
     * @param string $method
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function request(string $endpoint, array $params = [], string $method = "GET")
    {
        throw_if(is_null($this->apikey), new InvalidArgumentException('API key is not set'));

        $params['apiKey'] = $this->apikey;

        throw_if(
            !in_array($method, ['GET', 'POST']),
            new \InvalidArgumentException("Use GET or POST as method.")
        );

        if ($method === 'GET') {
            $params = ["query" => $params];
        } else {
            $params = ["form_params" => $params];
        }

        try {
            $response = $this->httpClient->request($method, $this->endpoint($endpoint), $params);

            if ($response->getStatusCode() !== 200) {
                throw new Exception("Couldn't get a result now");
            }
        } catch (GuzzleException $e) {
            throw $e;
        }

        return json_decode((string)$response->getBody());
    }

    /**
     * @param string $endpoint
     *
     * @return string
     */
    protected function endpoint(string $endpoint): string
    {
        return self::BASE_API_URL . $endpoint;
    }

    /**
     * @return string
     */
    public function getApikey(): string
    {
        return $this->apikey;
    }

    /**
     * @param string $apikey
     */
    public function setApikey(string $apikey): void
    {
        $this->apikey = $apikey;
    }
}
