<?php

namespace App\Services\Interpreter;

use App\Entity\Security;
use App\Factories\SecurityExpressionResponseFactory;
use App\Repository\Interfaces\SecurityRepositoryInterface;
use App\Responses\SecurityExpressionResponse;
use Doctrine\Common\Collections\ArrayCollection;

final class JSONInterpreterService implements InterpreterInterface
{
    public function __construct(private SecurityRepositoryInterface $repository)
    {
    }

    public function handleSecurityExpressions(Security $security, array $expressions): SecurityExpressionResponse
    {
        $rootExpressionOperator = $expressions['fn'] ?? '';

        $calculationScale = $expressions['calculation_scale'] ?? 0;

        $expressionA = $expressions['a'] ?? null;
        $expressionB = $expressions['b'] ?? null;

        $expressionAOutput = match (gettype($expressionA)) {
            'array' => $this->handleSecurityExpressions($security, $expressionA)->getResult()['output'],
            'integer' => $expressionA,
            'string' => $this->getTotalAttributeCount($this->repository->findFactsByAttributeName($security, $expressionA)),
            default => false
        };

        $expressionBOutput = match (gettype($expressionB)) {
            'array' => $this->handleSecurityExpressions($security, $expressionB)->getResult()['output'],
            'integer' => $expressionB,
            'string' => $this->getTotalAttributeCount($this->repository->findFactsByAttributeName($security, $expressionB)),
            default => false
        };

        if (!$expressionAOutput || !$expressionBOutput) {
            return SecurityExpressionResponseFactory::generateInvalidResponse('Failed expression');
        }

        /** @var string|false|null $expressionResult */
        $expressionResult = match ($rootExpressionOperator) {
            '*' => $this->multiply($expressionAOutput, $expressionBOutput, $calculationScale),
            '/' => $this->divide($expressionAOutput, $expressionBOutput, $calculationScale),
            '+' => $this->add($expressionAOutput, $expressionBOutput, $calculationScale),
            '-' => $this->subtract($expressionAOutput, $expressionBOutput, $calculationScale),
            default => false
        };

        if (is_null($expressionResult)) {
            return SecurityExpressionResponseFactory::generateInvalidResponse('Division By Zero');
        }

        if (!$expressionResult) {
            return SecurityExpressionResponseFactory::generateInvalidResponse('Operator Not Supported');
        }

        return SecurityExpressionResponseFactory::generateValidResponse((string) $expressionResult);
    }

    /**
     * Assumption here that domain allows multiple security facts to the same attribute
     * ie; ABC -> 4x Facts that all relate to the sales attribute
     * therefor i think it makes sense to add these values together to find the total for futher processing.
     */
    private function getTotalAttributeCount(ArrayCollection $facts, int $calculationScale = 0): string
    {
        $attributeTotal = 0;
        foreach ($facts as $fact) {
            $attributeTotal = $this->add(strval($attributeTotal), $fact['value'], $calculationScale);
        }

        return strval($attributeTotal);
    }

    private function multiply(string $a, string $b, int $calculationScale = 0): string
    {
        return bcmul($a, $b, $calculationScale);
    }

    private function divide(string $a, string $b, int $calculationScale = 0): string|null
    {
        try {
            return bcdiv($a, $b, $calculationScale);
        } catch (\DivisionByZeroError $exception) {
            return null;
        }
    }

    private function add(string $a, string $b, int $calculationScale = 0): string
    {
        return bcadd($a, $b, $calculationScale);
    }

    private function subtract(string $a, string $b, int $calculationScale = 0): string
    {
        return bcsub($a, $b, $calculationScale);
    }
}
