<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AnalyticsController
{
    #[Route('/api/analytics', name: 'analytics', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $postContent = (array) json_decode((string) $request->getContent(), true);
            $expression = $postContent['expression'];
            $security = $postContent['security'];
        } catch (\Exception $exception) {
            dump($exception->getMessage());
        }

        return new JsonResponse(['foo' => 'bar'], 200);
    }
}
