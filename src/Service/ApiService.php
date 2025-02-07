<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getProspects()
    {
        $url = 'http://localhost:8080/prospects';

        $response = $this->client->request('GET', $url);

        $data = $response->toArray();

        return $data;
    }

    public function getProspect($id)
    {
        $url = 'http://localhost:8080/prospects/' . $id;

        $response = $this->client->request('GET', $url);

        $data = $response->toArray();

        return $data;
    }

    public function addProspect($prospect)
    {
        $url = 'http://localhost:8080/prospects';

        $response = $this->client->request('POST', $url, [
            'json' => $prospect,
        ]);

        if ($response->getStatusCode() !== 201) {
            throw new \Exception('Failed to add prospect: ' . $response->getContent(false));
        }

        return $response->toArray();
    }

    public function updateProspect($id, $prospect)
    {
        $url = 'http://localhost:8080/prospects/' . $id;

        $response = $this->client->request('PUT', $url, [
            'json' => $prospect,
        ]);

        $data = $response->toArray();

        return $data;
    }

    public function deleteProspect($id)
    {
        $url = 'http://localhost:8080/prospects/' . $id;

        $response = $this->client->request('DELETE', $url);

        if ($response->getStatusCode() !== 204) {
            throw new \Exception('Failed to delete prospect: ' . $response->getContent(false));
        }

        return true;
    }
}
