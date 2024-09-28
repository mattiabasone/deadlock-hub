<?php

declare(strict_types=1);

namespace DeadlockHub\Tests\GameNews\PlayDeadlock;

use DeadlockHub\GameNews\PlayDeadlock\RssFetcher;
use FeedIo\Adapter\ClientInterface;
use FeedIo\Adapter\ResponseInterface;
use FeedIo\FeedIo;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RssFetcherTest extends KernelTestCase
{
    use ProphecyTrait;

    private ?RssFetcher $service;
    private ?ObjectProphecy $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = $this->prophesize(ClientInterface::class);

        $this->service = new RssFetcher(
            new FeedIo($this->client->reveal()),
            new NullLogger()
        );
    }

    public function testRssFetcherWithASuccessfulResponse(): void
    {
        $this->givenASuccessfulClientResponseForTheChangelogFeed();
        $result = $this->service->fetchChangelogFeed();

        self::assertCount(20, $result);

        $first = $result->first();
        self::assertEquals("33816", $first->id);
        self::assertEquals("09-27-2024 Update", $first->title);
        self::assertEquals(new \DateTimeImmutable("2024-09-28T00:33:18.000000+0000"), $first->publishedAt);
        self::assertEquals("https://forums.playdeadlock.com/threads/09-27-2024-update.33816/", $first->link);
        self::assertEquals(
            <<<HTML
                <div class="bbWrapper">- Mirage: Tornado lift duration reduced from 1.5s to 1.2s<br />
                - Mirage: Tornado T1 bonus lift duration reduced from +0.5s to +0.4s<br />
                - Mirage: Tornado base bullet evasion reduced from 30% to 25%<br />
                - Mirage: Djinn&#039;s Mark T3 now also reduces Multiplier Cooldown by 0.5s</div>
                HTML,
            $first->content
        );
    }

    private function givenASuccessfulClientResponseForTheChangelogFeed(): void
    {
        $response = $this->prophesize(ResponseInterface::class);
        $response->getBody()->willReturn(
            file_get_contents(__DIR__."/raw_data/feed.rss")
        );

        $response->isModified()
            ->willReturn(true);

        $this->client->getResponse(
            Argument::exact("https://forums.playdeadlock.com/forums/changelog.10/index.rss"),
            Argument::type(\DateTime::class)
        )->willReturn($response->reveal())
            ->shouldBeCalled();
    }
}
