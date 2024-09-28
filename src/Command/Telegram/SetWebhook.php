<?php

declare(strict_types=1);

namespace DeadlockHub\Command\Telegram;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Telegram\Bot\Api;

#[AsCommand(
    name: 'deadlock-hub:telegram:set-webhook',
    description: 'Set telegram webhook',
)]
class SetWebhook extends Command
{
    public function __construct(
        private readonly Api $telegramApi,
        #[Autowire('%env(string:APP_URL)%')]
        private readonly string $baseUrl,
        #[Autowire('%env(string:TELEGRAM_WEBHOOK_TOKEN)%')]
        private readonly string $telegramWebhookToken
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $url = $this->baseUrl.'/api/telegram/webhook/'.$this->telegramWebhookToken;
        $this->telegramApi->setWebhook(['url' => $url]);
        $io->success('Telegram webhook set: '.$url);

        return Command::SUCCESS;
    }
}
