<?php

namespace Puleeno\NhanhVn\Exceptions;

use Exception;

/**
 * Network Exception - Xử lý lỗi network/connection
 */
class NetworkException extends Exception
{
    public function __construct(string $message = "", int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
