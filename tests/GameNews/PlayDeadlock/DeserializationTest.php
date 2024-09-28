<?php

declare(strict_types=1);

namespace DeadlockHub\Tests\GameNews\PlayDeadlock;

use FeedIo\Adapter\FileSystem\Client;
use FeedIo\FeedIo;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DeserializationTest extends KernelTestCase
{
    use ProphecyTrait;

    public function testDeserializeFeed(): void
    {
        $feedIo = new FeedIo(new Client());

        // read a feed
        $result = $feedIo->read(__DIR__."/raw_data/feed.rss");

        // get title
        self::assertEquals(
            "Changelog",
            $result->getFeed()->getTitle()
        );

        self::assertCount(
            20,
            $result->getFeed(),
        );

        /** @var \FeedIo\Feed\Item $first */
        $first = iterator_to_array($result->getFeed())[0];
        dump($first);
        self::assertEquals("09-27-2024 Update", $first->getTitle());
        self::assertEquals("https://forums.playdeadlock.com/threads/09-27-2024-update.33816/", $first->getLink());
        self::assertEquals(
            <<<HTML
                <div class="bbWrapper">- Mirage: Tornado lift duration reduced from 1.5s to 1.2s<br />
                - Mirage: Tornado T1 bonus lift duration reduced from +0.5s to +0.4s<br />
                - Mirage: Tornado base bullet evasion reduced from 30% to 25%<br />
                - Mirage: Djinn&#039;s Mark T3 now also reduces Multiplier Cooldown by 0.5s</div>
                HTML,
            $first->getValue('content:encoded')
        );
    }
}
