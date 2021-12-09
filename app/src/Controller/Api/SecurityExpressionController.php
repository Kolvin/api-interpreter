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
            $postContent = (array) json_decode((string) $request->getContent(), true);
            $security = $this->securityRepository->findOneBy(['symbol' => $postContent['security']]);
            $expression = (array) $postContent['expression'];

            if (!$security instanceof Security) {
                throw new \Exception('security not found');
            }

            $response = $this->interpreter->handleSecurityExpression($security, $expression);
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
