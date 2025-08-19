<?php

namespace Puleeno\NhanhVn\Exceptions;

/**
 * Exception được throw khi không có quyền truy cập
 */
class UnauthorizedException extends ApiException
{
    public function __construct(
        string $message = "Không có quyền truy cập",
        int $code = 0,
        \Throwable $previous = null,
        int $httpStatusCode = 403,
        string $responseBody = ""
    ) {
        parent::__construct($message, $code, $previous, $httpStatusCode, $responseBody);
    }
}
