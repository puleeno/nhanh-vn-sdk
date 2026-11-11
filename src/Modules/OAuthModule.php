<?php

namespace Puleeno\NhanhVn\Modules;

use Puleeno\NhanhVn\Services\OAuthService;
use Puleeno\NhanhVn\Contracts\LoggerInterface;

/**
 * OAuth Module
 */
class OAuthModule
{
    private OAuthService $oauthService;
    private LoggerInterface $logger;

    public function __construct(OAuthService $oauthService, LoggerInterface $logger)
    {
        $this->oauthService = $oauthService;
        $this->logger = $logger;
    }

    /**
     * Exchange access code cho access token (v3 hỗ trợ verifyCode)
     */
    public function exchangeAccessCode(string $accessCode, ?string $verifyCode = null): array
    {
        return $this->oauthService->exchangeAccessCode($accessCode, $verifyCode);
    }

    /**
     * Lấy OAuth URL (v3: service, verifyCode)
     */
    public function getOAuthUrl(string $returnLink, ?string $verifyCode = null, ?string $service = null): string
    {
        return $this->oauthService->getOAuthUrl($returnLink, $verifyCode, $service);
    }
}
