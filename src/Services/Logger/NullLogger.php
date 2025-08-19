<?php

namespace Puleeno\NhanhVn\Services\Logger;

use Puleeno\NhanhVn\Contracts\LoggerInterface;

/**
 * Null Logger - Không làm gì cả, dùng khi không cần logging
 */
class NullLogger implements LoggerInterface
{
    public function debug(string $message, array $context = []): void
    {
        // Không làm gì
    }

    public function info(string $message, array $context = []): void
    {
        // Không làm gì
    }

    public function warning(string $message, array $context = []): void
    {
        // Không làm gì
    }

    public function error(string $message, array $context = []): void
    {
        // Không làm gì
    }

    public function critical(string $message, array $context = []): void
    {
        // Không làm gì
    }
}
