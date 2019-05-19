<?php

namespace YouCan\CurrencyConverter\Datasources\FixerIO;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use YouCan\CurrencyConverter\Converter as IConverter;

class Converter implements IConverter
{
    const BASE_API_URL = "data.fixer.io/api/";

    /** @var \GuzzleHttp\Client */
    protected $httpClient;

    /** @var string */
    private $access_key = null;

    protected $onFreePlan = false;

    public function __construct(Client $httpClient, string $access_key = null)
    {
        $this->httpClient = $httpClient;
        $this->access_key = $access_key;
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
        $params = [];
        if (!$this->onFreePlan) {
            $params['base'] = $baseCurrency;
        }

        if (count($toCurrencies) > 0) {
            $params['symbols'] = implode(',', $toCurrencies);
        }

        $response = (array)$this->request('latest', $params);

        throw_if(
            !isset($response['success']) || $response['success'] == false,
            new Exception("Couldn't get a result now")
        );

        return $response;
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
        throw_if(is_null($this->access_key), new InvalidArgumentException('access key is not set'));

        $params['access_key'] = $this->access_key;

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
        return sprintf("%s://%s%s", $this->onFreePlan ? 'http' : 'https', self::BASE_API_URL, $endpoint);
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
        $response = (array)$this->request('convert', ['from' => $from, 'to' => $to, 'amount' => $amount]);

        throw_if(
            !isset($response['success']) || $response['success'] == false || !isset($response['result']),
            new Exception("Couldn't get a result now")
        );

        return (float)$response['result'];
    }

    /**
     * @return string
     */
    public function getAccessKey(): string
    {
        return $this->access_key;
    }

    /**
     * @param string $access_key
     */
    public function setAccessKey(string $access_key): void
    {
        $this->access_key = $access_key;
    }

    /**
     * @return $this
     */
    public function paidPlan()
    {
        $this->onFreePlan = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function freePlan()
    {
        $this->onFreePlan = true;

        return $this;
    }
}
