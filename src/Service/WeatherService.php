<?php
declare(strict_types=1);

// src/Service/WeatherService.php

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherService
{
    const API_KEY = '0a402effa2d4c7ba806bf1eb904c4f99';
    private HttpClientInterface $client;
    private string $apiKey;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getWeatherData($city)
    {
        $response = $this->client->request('GET', 'https://api.openweathermap.org/data/2.5/weather', [
            'query' => [
                'q' => $city,
                'appid' => $this::API_KEY,
                'units' => 'metric' // Temperature set Celsius
            ]
        ]);

        // check status
        if ($response->getStatusCode() === 200) {
            return $response->toArray(); // convert data to JSON
        }

        throw new \Exception('Error fetching weather data: ' . $response->getStatusCode());
    }
}