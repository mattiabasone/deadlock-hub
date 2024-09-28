<?php

declare(strict_types=1);

namespace DeadlockHub\Tests\Steam;

use DeadlockHub\Steam\GetNewsForAppResponse;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\SerializerInterface;

class GetNewsForAppResponseTest extends KernelTestCase
{
    public function testGetNewsForAppResponseDeserialization(): void
    {
        $serializer = self::getContainer()->get(SerializerInterface::class);
        $getNewsForAppResponse = $serializer->deserialize(
            file_get_contents(__DIR__.'/raw_data/news.json'),
            GetNewsForAppResponse::class,
            'json'
        );

        self::assertInstanceOf(GetNewsForAppResponse::class, $getNewsForAppResponse);
        self::assertCount(4, $getNewsForAppResponse->appNews->newsItems);

        $firstNews = $getNewsForAppResponse->appNews->newsItems->first();
        self::assertEquals("PATCH 01.000.203", $firstNews->title);
        self::assertEquals(
            "https://steamstore-a.akamaihd.net/news/externalpost/steam_community_announcements/6474562312198731215",
            $firstNews->url
        );
        self::assertEquals(1713258041, $firstNews->date);
    }
}
