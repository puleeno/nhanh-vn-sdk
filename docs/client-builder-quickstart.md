# 🚀 Nhanh Client Builder - Quick Start

## 📋 Tổng quan

**Nhanh Client Builder** là một class sử dụng **Builder Pattern** để tạo `NhanhVnClient` một cách dễ dàng và trực quan.

## ⚡ Cách sử dụng nhanh

### 1. Tạo client cơ bản

```php
use Puleeno\NhanhVn\Client\NhanhClientBuilder;

$client = NhanhClientBuilder::create()
    ->withAppId('your_app_id')
    ->withBusinessId('your_business_id')
    ->withAccessToken('your_access_token')
    ->build();
```

### 2. Sử dụng convenience methods

```php
// Tạo client nhanh
$client = NhanhClientBuilder::createBasic(
    'your_app_id',
    'your_business_id',
    'your_access_token'
);

// Tạo client cho development
$client = NhanhClientBuilder::createDevelopment(
    'your_app_id',
    'your_business_id',
    'your_access_token'
);

// Tạo client cho production
$client = NhanhClientBuilder::createProduction(
    'your_app_id',
    'your_business_id',
    'your_access_token'
);
```

### 3. Tạo client với logging

```php
$client = NhanhClientBuilder::create()
    ->withAppId('your_app_id')
    ->withBusinessId('your_business_id')
    ->withAccessToken('your_access_token')
    ->withLogger()
    ->withLogLevel('DEBUG')
    ->withConsoleLogging()
    ->build();
```

### 4. Từ file config

```php
$client = NhanhClientBuilder::fromConfigFile('config/nhanh.json')->build();
```

### 5. Từ environment variables

```php
$client = NhanhClientBuilder::fromEnvironment()->build();
```

### 6. OAuth flow

```php
$client = NhanhClientBuilder::fromOAuth()
    ->withAppId('your_app_id')
    ->withSecretKey('your_secret_key')
    ->withRedirectUrl('https://your-app.com/callback')
    ->build();
```

## 🎯 Environment Presets

```php
// Development: DEBUG logging, console output, SSL disabled
->forDevelopment()

// Production: WARNING logging, file logging, SSL enabled
->forProduction()

// Testing: DEBUG logging, console output, SSL disabled
->forTesting()
```

## 📝 Logging Options

```php
->withLogger()                    // Bật logging
->withLogLevel('DEBUG')          // DEBUG, INFO, WARNING, ERROR, CRITICAL
->withLogFile('logs/app.log')    // Log file path
->withConsoleLogging()           // Log ra console
->withFileLogging()              // Log ra file
->withLogRotation(7)             // Log rotation (ngày)
```

## 🔧 Configuration Options

```php
->withApiVersion('2.0')          // API version
->withTimeout(30)                // Timeout (giây)
->withRetryAttempts(3)           // Số lần retry
->withRateLimit(150)             // Rate limit
->withSSLValidation(true)        // SSL validation
```

## ✅ Validation

Builder tự động validate configuration trước khi build:

- **App ID** bắt buộc
- **Business ID** và **Access Token** bắt buộc (trừ OAuth)
- **Secret Key** và **Redirect URL** bắt buộc cho OAuth

## 🚀 Migration từ code cũ

### Code cũ
```php
$config = new ClientConfig([
    'appId' => 'your_app_id',
    'businessId' => 'your_business_id',
    'accessToken' => 'your_access_token'
]);
$client = NhanhVnClient::getInstance($config);
```

### Code mới
```php
$client = NhanhClientBuilder::create()
    ->withAppId('your_app_id')
    ->withBusinessId('your_business_id')
    ->withAccessToken('your_access_token')
    ->build();
```

## 📚 Tài liệu đầy đủ

Xem [Tài liệu chi tiết](client-builder.md) để biết thêm thông tin về:

- Tất cả các methods có sẵn
- Best practices
- Testing
- Troubleshooting
- API Reference

## 🎉 Kết luận

**Nhanh Client Builder** giúp bạn tạo `NhanhVnClient` một cách dễ dàng với syntax gọn gàng và trực quan. Sử dụng Builder Pattern cho configuration phức tạp và Static Methods cho configuration đơn giản!
