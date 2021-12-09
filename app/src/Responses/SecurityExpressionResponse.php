<?php

declare(strict_types=1);

namespace App\Responses;

final class SecurityExpressionResponse
{
    /**
     * @param array<string, mixed> $result
     * @param array<string, mixed> $notices
     * @param int                  $statusCode
     */
    public function __construct(private array $result, private array $notices = [], private int $statusCode = 200)
    {
    }

    /**
     * @return mixed[]
     */
    public function getResult(): array
    {
        return $this->result;
    }

    /**
     * @return mixed[]
     */
    public function getNotices(): array
    {
        return $this->notices;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
