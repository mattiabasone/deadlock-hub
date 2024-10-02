<?php

declare(strict_types=1);

namespace DeadlockHub\Tests\GameNews;

use DeadlockHub\Entity\Enum\GameNewsType;
use DeadlockHub\GameNews\NewsHandler;
use DeadlockHub\GameNews\Steam\GetNewsForAppResponse;
use DeadlockHub\GameNews\Steam\SteamNewsApi;
use DeadlockHub\GameNews\SteamNewsService;
use DeadlockHub\Message\Telegram\GameNewsAdded;
use DeadlockHub\Repository\GameNewsRepository;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Group('unit')]
#[Group('steam')]
class SteamNewsServiceTest extends KernelTestCase
{
    private ?MockObject $steamNewsApi;
    private ?MockObject $gameNewsRepository;
    private ?MockObject $messageBus;

    public function setUp(): void
    {
        parent::setUp();

        $this->steamNewsApi = $this->createMock(SteamNewsApi::class);
        $this->steamNewsApi->method('fetch')->willReturn(
            self::getContainer()
                ->get(SerializerInterface::class)
                ->deserialize(
                    file_get_contents(__DIR__.'/Steam/raw_data/news.json'),
                    GetNewsForAppResponse::class,
                    'json'
                )
        );

        $this->gameNewsRepository = $this->createMock(GameNewsRepository::class);

        $this->messageBus = $this->createMock(MessageBusInterface::class);
    }

    #[Test]
    public function shouldNotProcessOldNews(): void
    {
        $newsHandler = $this->createNewsHandlerInstance(new \DateTimeImmutable());

        $steamNewsService = new SteamNewsService(
            $this->steamNewsApi,
            $newsHandler
        );

        $steamNewsService->processNews();

        $this->shouldNotRecordAndDispatchAnyNews();
    }

    #[Test]
    public function shouldProcessFirstNews(): void
    {
        $newsHandler = $this->createNewsHandlerInstance(new \DateTimeImmutable('2024-09-27T19:20:00+00:00'));

        $steamNewsService = new SteamNewsService(
            $this->steamNewsApi,
            $newsHandler
        );

        $this->shoudlStoreAndPublishNews("6339469370186650798");

        $steamNewsService->processNews();
    }

    #[Test]
    public function shouldProcessMultipleNews(): void
    {
        $newsHandler = $this->createNewsHandlerInstance(new \DateTimeImmutable('2024-09-27T16:05:00+00:00'));

        $steamNewsService = new SteamNewsService(
            $this->steamNewsApi,
            $newsHandler
        );

        $this->shoudlStoreAndPublishNews("6339469370186650798", "6339469370186244811", "6339469370186087577");

        $steamNewsService->processNews();
    }

    private function createNewsHandlerInstance(\DateTimeImmutable $now): NewsHandler
    {
        return new NewsHandler(
            $this->gameNewsRepository,
            $this->messageBus,
            new MockClock($now),
            self::getContainer()->get(LoggerInterface::class)
        );
    }

    private function shouldNotRecordAndDispatchAnyNews(): void
    {
        $this->gameNewsRepository->expects(
            $this->never()
        )->method('store');

        $this->messageBus->expects(
            $this->never()
        )->method('dispatch');
    }

    private function shoudlStoreAndPublishNews(string ...$identifiers): void
    {
        $this->gameNewsRepository->expects($this->exactly(\count($identifiers)))
            ->method('store')
            ->willReturnCallback(
                function (string $identifier, GameNewsType $type, string $content) use ($identifiers) {
                    self::assertEquals(GameNewsType::SteamNews, $type);

                    if (!\in_array($identifier, $identifiers, true)) {
                        self::fail("Unexpected news item");
                    }
                }
            );

        $this->messageBus->expects($this->exactly(\count($identifiers)))
            ->method('dispatch')
            ->willReturnCallback(
                function (GameNewsAdded $message) use ($identifiers) {
                    self::assertInstanceOf(GameNewsAdded::class, $message);
                    self::assertEquals(GameNewsType::SteamNews, $message->type);

                    if (!\in_array($message->identifier, $identifiers, true)) {
                        self::fail("Unexpected news item");
                    }

                    return new Envelope($message);
                }
            );
    }
}
