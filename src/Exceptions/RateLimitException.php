<?php

namespace Puleeno\NhanhVn\Exceptions;

/**
 * Exception được throw khi vượt quá rate limit
 */
class RateLimitException extends ApiException
{
    private int $lockedSeconds;
    private int $unlockedAt;

    public function __construct(
        string $message = "",
        int $code = 0,
        \Throwable $previous = null,
        int $httpStatusCode = 429,
        string $responseBody = "",
        int $lockedSeconds = 0,
        int $unlockedAt = 0
    ) {
        parent::__construct($message, $code, $previous, $httpStatusCode, $responseBody);
        $this->lockedSeconds = $lockedSeconds;
        $this->unlockedAt = $unlockedAt;
    }

    /**
     * Lấy số giây bị khóa
     */
    public function getLockedSeconds(): int
    {
        return $this->lockedSeconds;
    }

    /**
     * Lấy thời gian được mở khóa (Unix timestamp)
     */
    public function getUnlockedAt(): int
    {
        return $this->unlockedAt;
    }

    /**
     * Lấy thời gian được mở khóa (formatted)
     */
    public function getUnlockedAtFormatted(): string
    {
        return date('Y-m-d H:i:s', $this->unlockedAt);
    }

    /**
     * Kiểm tra có còn bị khóa không
     */
    public function isStillLocked(): bool
    {
        return time() < $this->unlockedAt;
    }

    /**
     * Lấy thời gian còn lại bị khóa
     */
    public function getRemainingLockTime(): int
    {
        $remaining = $this->unlockedAt - time();
        return max(0, $remaining);
    }
}
