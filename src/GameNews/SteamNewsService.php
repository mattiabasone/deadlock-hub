<?php

declare(strict_types=1);

namespace DeadlockHub\GameNews;

use DeadlockHub\Entity\Enum\GameNewsType;
use DeadlockHub\GameNews\Steam\GetNewsForAppResponse\NewsItem;
use DeadlockHub\GameNews\Steam\SteamNewsApi;

readonly class SteamNewsService implements NewsServiceInterface
{
    public function __construct(
        private SteamNewsApi $steamNewsApi,
        private NewsHandler $newsServiceHandler,
    ) {
    }

    public function processNews(): void
    {
        $news = $this->steamNewsApi->fetch();

        $streamableNews = $news->appNews
            ->newsItems
            ->filter(fn (NewsItem $newsItem): bool => $this->newsServiceHandler->shouldStreamNews(
                GameNewsType::SteamNews,
                $newsItem->gid,
                $newsItem->getDateTime()
            ));

        foreach ($streamableNews as $newsItem) {

            $identifier = $newsItem->gid;
            $message = <<<MESSAGE
                📰 <b>{$newsItem->title}</b>
                
                {$newsItem->url}
                MESSAGE;

            $this->newsServiceHandler->recordNews(GameNewsType::SteamNews, $identifier, $message);
        }
    }
}
