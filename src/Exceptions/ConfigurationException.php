<?php

namespace Puleeno\NhanhVn\Exceptions;

/**
 * Exception được throw khi có lỗi trong cấu hình
 */
class ConfigurationException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
