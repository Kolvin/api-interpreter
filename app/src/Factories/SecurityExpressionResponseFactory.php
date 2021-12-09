<?php

declare(strict_types=1);

namespace App\Factories;

use App\Responses\SecurityExpressionResponse;
use JetBrains\PhpStorm\Pure;

final class SecurityExpressionResponseFactory
{
    #[Pure] public static function generateInvalidResponse(string $message): SecurityExpressionResponse
    {
        return new SecurityExpressionResponse([], ['error' => $message], 400);
    }

    #[Pure] public static function generateValidResponse(string $calculationOutput): SecurityExpressionResponse
    {
        return new SecurityExpressionResponse(['output' => $calculationOutput], [], 400);
    }
}