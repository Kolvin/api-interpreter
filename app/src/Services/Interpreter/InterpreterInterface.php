<?php

declare(strict_types=1);

namespace App\Services\Interpreter;

use App\Entity\Security;
use App\Responses\SecurityExpressionResponse;

interface InterpreterInterface
{
    /**
     * @param array<string, mixed> $expression
     */
    public function handleSecurityExpression(Security $security, array $expression): SecurityExpressionResponse;
}
