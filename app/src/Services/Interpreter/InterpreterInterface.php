<?php

declare(strict_types=1);

namespace App\Services\Interpreter;

use App\Entity\Security;
use App\Responses\SecurityExpressionResponse;

interface InterpreterInterface
{
    /**
     * @param array<string, mixed> $expressions
     */
    public function handleSecurityExpressions(Security $security, array $expressions): SecurityExpressionResponse;
}
