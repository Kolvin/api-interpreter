<?php

declare(strict_types=1);

namespace App\Responses;

final class SecurityExpressionResponse
{
    public function __construct(private array $result, private array $notices = [], private int $statusCode = 200)
    {
    }

    public function getResult(): array
    {
        return $this->result;
    }

    public function getNotices(): array
    {
        return $this->notices;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}