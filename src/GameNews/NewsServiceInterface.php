<?php

declare(strict_types=1);

namespace DeadlockHub\GameNews;

interface NewsServiceInterface
{
    public function processNews(): void;
}
