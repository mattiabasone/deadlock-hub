<?php

declare(strict_types=1);

namespace DeadlockHub\Telegram\BotCommand;

use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{
    protected string $name = 'start';
    protected array $aliases = ['help'];
    protected string $description = 'Start Command to get you started';

    public function handle(): void
    {
        $this->replyWithMessage([
            'text' => <<<TEXT
                Hello!
                
                This bot collects some news from the Deadlock API/newsfeed.
                
                Available commands:
                /subscribe - subscribe the chat for game updates
                /unsubscribe - remove the subscription
                TEXT,
        ]);
    }
}
