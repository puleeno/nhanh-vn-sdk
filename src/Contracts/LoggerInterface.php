<?php

namespace Puleeno\NhanhVn\Contracts;

/**
 * Logger Interface cho Nhanh.vn SDK
 */
interface LoggerInterface
{
    /**
     * Log debug message
     */
    public function debug(string $message, array $context = []): void;

    /**
     * Log info message
     */
    public function info(string $message, array $context = []): void;

    /**
     * Log warning message
     */
    public function warning(string $message, array $context = []): void;

    /**
     * Log error message
     */
    public function error(string $message, array $context = []): void;

    /**
     * Log critical message
     */
    public function critical(string $message, array $context = []): void;
}
