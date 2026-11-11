<?php

namespace Puleeno\NhanhVn\Tests\Unit\Config;

use PHPUnit\Framework\TestCase;
use Puleeno\NhanhVn\Config\ClientConfig;

class ClientConfigTest extends TestCase
{
    private function oauthConfig(array $overrides = []): ClientConfig
    {
        return new ClientConfig(array_merge([
            'appId' => '78822',
            'secretKey' => 'secret',
            'returnLink' => 'https://example.com/callback',
        ], $overrides));
    }

    public function testGetOAuthUrlV3ContainsRequiredParams(): void
    {
        $config = $this->oauthConfig();
        $url = $config->getOAuthUrl('Abc123VerifyCode45', 'pos');
        $parts = parse_url($url);
        parse_str($parts['query'] ?? '', $q);

        $this->assertSame('https', $parts['scheme'] ?? '');
        $this->assertSame('nhanh.vn', $parts['host'] ?? '');
        $this->assertSame('/oauth', $parts['path'] ?? '');
        $this->assertSame('78822', $q['appId'] ?? null);
        $this->assertSame('pos', $q['service'] ?? null);
        $this->assertSame('Abc123VerifyCode45', $q['verifyCode'] ?? null);
        $this->assertSame('https://example.com/callback', $q['returnLink'] ?? null);
        $this->assertArrayNotHasKey('version', $q);
        $this->assertArrayNotHasKey('businessId', $q);
    }

    public function testGetOAuthUrlUsesConfiguredServiceAndVerifyCode(): void
    {
        $config = $this->oauthConfig([
            'oauthService' => 'ship',
            'verifyCode' => 'StoredVerify12345',
        ]);
        $parts = parse_url($config->getOAuthUrl());
        parse_str($parts['query'] ?? '', $q);
        $this->assertSame('ship', $q['service'] ?? null);
        $this->assertSame('StoredVerify12345', $q['verifyCode'] ?? null);
    }

    public function testGetOAuthUrlDefaultsServiceToPos(): void
    {
        $config = $this->oauthConfig();
        parse_str(parse_url($config->getOAuthUrl('verifyCode123456'))['query'] ?? '', $q);
        $this->assertSame('pos', $q['service'] ?? null);
    }

    public function testGetOAuthUrlOmitsEmptyVerifyCode(): void
    {
        $config = $this->oauthConfig();
        parse_str(parse_url($config->getOAuthUrl(null, 'pos'))['query'] ?? '', $q);
        $this->assertArrayNotHasKey('verifyCode', $q);
    }

    public function testDefaultApiVersionIsV3(): void
    {
        $config = $this->oauthConfig();
        $this->assertSame('3.0', $config->getApiVersion());
    }

    public function testIsValidForOAuthFlow(): void
    {
        $config = $this->oauthConfig();
        $this->assertTrue($config->isValid());
    }

    public function testIsValidForApiCalls(): void
    {
        $config = new ClientConfig([
            'appId' => '78822',
            'businessId' => '110668',
            'accessToken' => 'token',
        ]);
        $this->assertTrue($config->isValid());
    }

    public function testOauthServiceGettersAndSetter(): void
    {
        $config = $this->oauthConfig();
        $this->assertSame('pos', $config->getOauthService());
        $config->setVerifyCode('newVerify1234567');
        $this->assertSame('newVerify1234567', $config->getVerifyCode());
    }
}
