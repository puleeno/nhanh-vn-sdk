<?php

namespace Puleeno\NhanhVn\Services\Logger;

use Monolog\Logger as MonologLogger;
use Puleeno\NhanhVn\Contracts\LoggerInterface;

/**
 * Monolog Adapter cho Nhanh.vn SDK
 */
class MonologAdapter implements LoggerInterface
{
    private MonologLogger $logger;

    public function __construct(MonologLogger $logger)
    {
        $this->logger = $logger;
    }

    public function debug(string $message, array $context = []): void
    {
        $this->logger->debug($message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }

    public function critical(string $message, array $context = []): void
    {
        $this->logger->critical($message, $context);
    }

    /**
     * Get underlying Monolog instance
     */
    public function getMonologLogger(): MonologLogger
    {
        return $this->logger;
    }
}
