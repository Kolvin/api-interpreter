<?php

namespace App\Controller\Api;

use App\Entity\Security;
use App\Repository\Interfaces\SecurityRepositoryInterface;
use App\Responses\ApiResponse;
use App\Responses\SecurityExpressionResponse;
use App\Services\Interpreter\InterpreterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class SecurityExpressionController
{
    public function __construct(private InterpreterInterface $interpreter, private SecurityRepositoryInterface $securityRepository)
    {
    }

    #[Route('/api/security-expressions', name: 'security-expressions', methods: ['POST'])]
    public function __invoke(Request $request): ApiResponse
    {
        try {
            /** @var array<string, array<string, mixed>|string|float> $postContent */
            $postContent = json_decode(strval($request->getContent()), true);
            $security = $this->securityRepository->findOneBy(['symbol' => $postContent['security']]);

            /** @var array<string, mixed> $expressions */
            $expressions = $postContent['expression'];

            if (!$security instanceof Security) {
                throw new \Exception('Security Not Found');
            }

            $response = $this->interpreter->handleSecurityExpressions($security, $expressions);
        } catch (\Exception $exception) {
            $response = new SecurityExpressionResponse([], ['error' => $exception->getMessage()], 400);
        }

        return new ApiResponse(
            data: $response->getResult(),
            notices: $response->getNotices(),
            statusCode: $response->getStatusCode()
        );
    }
}
