<?php

declare(strict_types=1);

namespace DeadlockHub\Entity\Enum;

enum GameNewsType: string
{
    case SteamNews = 'steam_news';
    case PlayDeadlockChangelogNews = 'play_deadlock_changelog_news';
}
