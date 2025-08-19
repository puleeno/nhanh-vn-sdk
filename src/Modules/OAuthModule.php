<?php

namespace Puleeno\NhanhVn\Modules;

use Puleeno\NhanhVn\Services\OAuthService;

/**
 * OAuth Module
 */
class OAuthModule
{
    private OAuthService $oauthService;

    public function __construct(OAuthService $oauthService)
    {
        $this->oauthService = $oauthService;
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
