<?php

declare(strict_types=1);

namespace DeadlockHub\Infrastructure\FeedIo;

use FeedIo\Adapter\ClientInterface;
use FeedIo\Adapter\Http\Client;
use GuzzleHttp\Client as GuzzleClient;

class ClientFactory
{
    public static function create(): ClientInterface
    {
        return new Client(
            new GuzzleClient(
                [
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
                    ],
                ]
            )
        );
    }
}
