<?php

declare(strict_types=1);

namespace DeadlockHub\Tests\GameNews\Steam;

use DeadlockHub\GameNews\Steam\GetNewsForAppResponse;
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
        self::assertCount(10, $getNewsForAppResponse->appNews->newsItems);

        $firstNews = $getNewsForAppResponse->appNews->newsItems->first();
        self::assertInstanceOf(GetNewsForAppResponse\NewsItem::class, $firstNews);
        self::assertEquals("Risk of Rain 2 co-creator now working on Valve multiplayer game Deadlock", $firstNews->title);
        self::assertEquals(
            "https://steamstore-a.akamaihd.net/news/externalpost/PCGamesN/6339469370186650798",
            $firstNews->url
        );
        self::assertEquals(1727465088, $firstNews->date);
        self::assertEquals(new \DateTimeImmutable("2024-09-27T19:24:48.000000+0000"), $firstNews->getDateTime());
    }
}
