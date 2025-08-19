<?php

namespace Puleeno\NhanhVn\Exceptions;

/**
 * Exception được throw khi dữ liệu không hợp lệ
 */
class InvalidDataException extends ApiException
{
    public function __construct(
        string $message = "Dữ liệu không hợp lệ",
        int $code = 0,
        \Throwable $previous = null,
        int $httpStatusCode = 400,
        string $responseBody = ""
    ) {
        parent::__construct($message, $code, $previous, $httpStatusCode, $responseBody);
    }
}
