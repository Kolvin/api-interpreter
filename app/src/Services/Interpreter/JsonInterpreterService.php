<?php

namespace App\Services\Interpreter;

use App\Entity\Security;
use App\Factories\SecurityExpressionResponseFactory;
use App\Repository\Interfaces\SecurityRepositoryInterface;
use App\Responses\SecurityExpressionResponse;
use Doctrine\Common\Collections\ArrayCollection;

final class JsonInterpreterService implements InterpreterInterface
{
    public function __construct(private SecurityRepositoryInterface $repository)
    {
    }

    public function handleSecurityExpression(Security $security, array $expression): SecurityExpressionResponse
    {
        // @TODO handle multiple expression to satisfy these requirement
        // an expression can be one of;
        //
        // name of attribute
        // an int
        // expression
        $expressionA = $expression['a'];
        $expressionB = $expression['b'];
        $calculationScale = $expression['calculation_scale'] ?? 0;

        // find facts matching attribute name for the requested security
        $facts = $this->repository->findFactsByAttributeName($security, (string) $expressionA);

        // assumption here that domain allows multiple security facts to the same attribute
        // ie; ABC -> 4x Facts that all relate to the sales attribute
        $attributeTotal = $this->getTotalAttributeCount($facts, $calculationScale);

        try {
            $expressionResult = match ($expression['fn']) {
                '*' => $this->multiply($attributeTotal, strval($expressionB), $calculationScale),
                '/' => $this->divide($attributeTotal, strval($expressionB), $calculationScale),
                '+' => $this->add($attributeTotal, strval($expressionB), $calculationScale),
                '-' => $this->subtract($attributeTotal, strval($expressionB), $calculationScale),
            };
        } catch (\UnhandledMatchError $exception) {
            return SecurityExpressionResponseFactory::generateInvalidResponse('Unsupported operator');
        }

        return SecurityExpressionResponseFactory::generateValidResponse($expressionResult);
    }

    private function getTotalAttributeCount(ArrayCollection $facts, int $calculationScale = 0): string
    {
        $attributeTotal = 0;
        foreach($facts as $fact)
        {
            $attributeTotal = $this->add($attributeTotal, $fact->getValue(), $calculationScale);
        }

        return $attributeTotal;
    }

    private function multiply(string $a, string $b, int $calculationScale = 0): string
    {
        return bcmul($a, $b, $calculationScale);
    }

    private function divide(string $a, string $b, int $calculationScale = 0): string
    {
        return bcdiv($a, $b, $calculationScale);
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
