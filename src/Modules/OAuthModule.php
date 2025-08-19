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
     * Exchange access code cho access token
     */
    public function exchangeAccessCode(string $accessCode): array
    {
        return $this->oauthService->exchangeAccessCode($accessCode);
    }

    /**
     * Lấy OAuth URL
     */
    public function getOAuthUrl(string $returnLink): string
    {
        return $this->oauthService->getOAuthUrl($returnLink);
    }
}
