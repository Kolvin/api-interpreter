<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController
{
    #[Route('/', name: 'home')]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['ping' => 'pong'], 200);
    }
}
