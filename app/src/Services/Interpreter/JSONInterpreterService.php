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

    /**
     * @param array<string, mixed> $expression
     */
    public function handleSecurityExpression(Security $security, array $expression): SecurityExpressionResponse
    {
        // @TODO handle multiple expression to satisfy these requirement
        // an expression can be one of;
        //
        // name of attribute
        // an int
        // expression
        $expressionA = strval($expression['a']);
        $expressionB = $expression['b'];

        /** @var int $calculationScale */
        $calculationScale = $expression['calculation_scale'] ?? 0;

        /** @var string $requestedOperator */
        $requestedOperator = $expression['fn'];

        // find facts matching attribute name for the requested security
        $facts = $this->repository->findFactsByAttributeName($security, $expressionA);

        // assumption here that domain allows multiple security facts to the same attribute
        // ie; ABC -> 4x Facts that all relate to the sales attribute
        $attributeTotal = $this->getTotalAttributeCount($facts, $calculationScale);

        /** @var string|false|null $expressionResult */
        $expressionResult = match ($requestedOperator) {
            '*' => $this->multiply($attributeTotal, strval($expressionB), $calculationScale),
            '/' => $this->divide($attributeTotal, strval($expressionB), $calculationScale),
            '+' => $this->add($attributeTotal, strval($expressionB), $calculationScale),
            '-' => $this->subtract($attributeTotal, strval($expressionB), $calculationScale),
            default => false
        };

        if (is_null($expressionResult)) {
            return SecurityExpressionResponseFactory::generateInvalidResponse('Division By Zero');
        }

        if (!$expressionResult) {
            return SecurityExpressionResponseFactory::generateInvalidResponse('Division By Zero');
        }

        return SecurityExpressionResponseFactory::generateValidResponse($expressionResult);
    }

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
