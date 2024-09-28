<?php

declare(strict_types=1);

namespace DeadlockHub\Controller;

use DeadlockHub\Telegram\TelegramService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Telegram\Bot\Objects\Update;

#[Route('/api/telegram')]
class TelegramController extends AbstractController
{
    public function __construct(
        #[Autowire('%env(string:TELEGRAM_WEBHOOK_TOKEN)%')]
        private readonly string $telegramWebhookToken,
        private readonly TelegramService $telegramService,
        private readonly LoggerInterface $logger
    ) {
    }

    #[Route('/webhook/{token}', methods: 'POST')]
    public function webhook(Request $request, string $token): JsonResponse
    {
        if ($token !== $this->telegramWebhookToken) {
            return new JsonResponse(['ok' => false], Response::HTTP_NOT_FOUND);
        }

        try {
            $update = new Update(json_decode($request->getContent(), associative: true, flags: JSON_THROW_ON_ERROR));
            $this->telegramService->handleWebhookUpdate($update);

            $this->logger->debug("Telegram update received.");

            return new JsonResponse([
                'ok' => true,
                'message' => 'Update received',
            ]);
        } catch (\Throwable $exception) {
            $this->logger->error("Something went wrong handling Telegram Update", ['exception_message' => $exception->getMessage()]);

            return new JsonResponse([
                'ok' => false,
                'message' => 'Update received',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
