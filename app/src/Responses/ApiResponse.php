<?php

namespace App\Responses;

use Symfony\Component\HttpFoundation\JsonResponse;

final class ApiResponse extends JsonResponse
{
    /**
     * ApiResponse constructor.
     *
     * @param array<mixed>         $data
     * @param array<string, mixed> $notices
     * @param array<int, string>   $headers
     */
    public function __construct(array $data, array $notices = [], int $statusCode = 200, array $headers = [])
    {
        parent::__construct(['result' => $data, 'notices' => $notices], $statusCode, $headers);
    }
}
