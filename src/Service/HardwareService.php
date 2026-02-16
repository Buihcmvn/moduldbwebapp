<?php
declare(strict_types=1);

namespace App\Service;

use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HardwareService
{
    private HttpClientInterface $client;

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
     * @throws Exception
     */
    public function getHardwareData($id): array
    {
        // for more information about the HTTP Client --> https://symfony.com/doc/current/http_client.html
        $response = $this->client->request('GET', 'http://127.0.0.1:8001/hardware/get/' . $id);

        // check HTTP CODE
        if ($response->getStatusCode() === 200) {
            return $response->toArray(); // convert to JSON
        }

        throw new Exception('Error fetching hardware data: ' . $response->getStatusCode());
    }


    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function getHardwareListData(): array
    {
        $response = $this->client->request('GET', 'http://127.0.0.1:8001/hardware/list');

        // check HTTP CODE
        if ($response->getStatusCode() === 200) {
            return $response->toArray(); // convert to JSON
        }

        throw new Exception('Error fetching hardware data: ' . $response->getStatusCode());
    }


    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function createHardware($hardware): array
    {
//        dd($hardware);
        $response = $this->client->request('POST', 'http://127.0.0.1:8001/hardware/post', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => $hardware,
        ]);

        // check HTTP CODE
        if ($response->getStatusCode() === 201) {
            return $response->toArray(); // convert to JSON
        }

        throw new Exception('Error fetching hardware data: ' . $response->getStatusCode());
    }


    /**
     * @throws TransportExceptionInterface
     * @throws Exception
     * @throws DecodingExceptionInterface
     */
    public function updateHardware($hardwareId, $hardwareAenderung): array
    {
        $response = $this->client->request('PUT', 'http://127.0.0.1:8001/hardware/update/' . $hardwareId, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => $hardwareAenderung,
        ]);

        // check HTTP CODE
        if ($response->getStatusCode() === 200) {
            return $response->toArray();
        }
        throw new Exception('Error fetching hardware data:' . $response->getStatusCode());
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function deleteHardware($id): array
    {
        // Send a DELETE request to the specified endpoint
        $response = $this->client->request('DELETE', 'http://127.0.0.1:8001/hardware/delete/' . $id);

        // check HTTP CODE
        if ($response->getStatusCode() === 200) {
            return ['message' => 'Hardware deleted successfully.'];
        }

        // Handle other status codes
        throw new Exception('Error fetching hardware data: ' . $response->getStatusCode());
    }
}
