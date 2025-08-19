<?php

namespace Puleeno\NhanhVn\Exceptions;

use Exception;

/**
 * API Exception - Xử lý lỗi từ Nhanh.vn API
 */
class ApiException extends Exception
{
    public function __construct(string $message = "", int $code = 0, ?Exception $previous = null)
    {
        return $this->responseBody;
    }

    /**
     * Lấy error code
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * Lấy error data
     */
    public function getErrorData(): array
    {
        return $this->errorData;
    }

    /**
     * Kiểm tra có phải lỗi client (4xx) không
     */
    public function isClientError(): bool
    {
        return $this->httpStatusCode >= 400 && $this->httpStatusCode < 500;
    }

    /**
     * Kiểm tra có phải lỗi server (5xx) không
     */
    public function isServerError(): bool
    {
        return $this->httpStatusCode >= 500;
    }

    /**
     * Kiểm tra có phải lỗi authentication không
     */
    public function isAuthenticationError(): bool
    {
        return $this->httpStatusCode === 401;
    }

    /**
     * Kiểm tra có phải lỗi authorization không
     */
    public function isAuthorizationError(): bool
    {
        return $this->httpStatusCode === 403;
    }

    /**
     * Kiểm tra có phải lỗi not found không
     */
    public function isNotFoundError(): bool
    {
        return $this->httpStatusCode === 404;
    }

    /**
     * Kiểm tra có phải lỗi rate limit không
     */
    public function isRateLimitError(): bool
    {
        return $this->httpStatusCode === 429;
    }

    /**
     * Kiểm tra có phải lỗi invalid data không
     */
    public function isInvalidDataError(): bool
    {
        return strpos($this->message, 'Invalid data') !== false;
    }
}
