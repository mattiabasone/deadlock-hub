<?php

declare(strict_types=1);

namespace DeadlockHub\Steam;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SteamNewsApi
{
    private HttpClientInterface $client;

    public function __construct(
        HttpClientInterface $client,
        private readonly SerializerInterface $serializer
    ) {
        $this->client = $client->withOptions([
            'headers' => [
                'Accept' => 'application/json',
                'Accept-Language' => 'en-US,en;q=0.9',
                'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
            ],
        ]);
    }

    public function fetch(): GetNewsForAppResponse
    {
        $response = $this->client->request(
            'GET',
            'https://api.steampowered.com/ISteamNews/GetNewsForApp/v2/?appid=1422450&count=10'
        );

        return $this->serializer->deserialize(
            $response->getContent(),
            GetNewsForAppResponse::class,
            'json'
        );
    }
}
