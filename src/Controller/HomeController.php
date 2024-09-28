<?php

declare(strict_types=1);

namespace DeadlockHub\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    public function __construct()
    {
    }

    #[Route('/', methods: 'GET')]
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'ok' => true,
            'message' => 'Hello world',
        ]);
    }
}
