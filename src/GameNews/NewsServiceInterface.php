<?php

declare(strict_types=1);

namespace DeadlockHub\GameNews;

// TODO: create a "BaseNewsService" abstract class with the logic in common between the two services
interface NewsServiceInterface
{
    public function processNews(): void;
}
