# 🚀 Nhanh Client Builder - Hướng dẫn sử dụng

## 📋 Tổng quan

**Nhanh Client Builder** là một class sử dụng **Builder Pattern** để tạo `NhanhVnClient` một cách dễ dàng và trực quan. Thay vì phải tạo `ClientConfig` thủ công, developer có thể sử dụng fluent interface để thiết lập client một cách gọn gàng.

## ✨ Tính năng chính

- 🎯 **Fluent Interface** - Syntax gọn gàng, dễ đọc
- 🔧 **Flexible Configuration** - Nhiều cách thiết lập khác nhau
- 🌍 **Environment Support** - Hỗ trợ development, production, testing
- 📝 **Logging Integration** - Tích hợp Monolog với nhiều tùy chọn
- 🔐 **OAuth Support** - Hỗ trợ OAuth flow
- ✅ **Validation** - Tự động validate configuration trước khi build
- 🚀 **Static Convenience Methods** - Các method tiện ích để tạo client nhanh

## 🏗️ Cách sử dụng cơ bản

### 1. Tạo client với config cơ bản

```php
use Puleeno\NhanhVn\Client\NhanhClientBuilder;

$client = NhanhClientBuilder::create()
    ->withAppId('your_app_id')
    ->withBusinessId('your_business_id')
    ->withAccessToken('your_access_token')
    ->build();
```

### 2. Tạo client với logging

```php
$client = NhanhClientBuilder::create()
    ->withAppId('your_app_id')
    ->withBusinessId('your_business_id')
    ->withAccessToken('your_access_token')
    ->withLogger()
    ->withLogLevel('DEBUG')
    ->withLogFile('logs/nhanh.log')
    ->withConsoleLogging()
    ->build();
```

### 3. Tạo client cho development

```php
$client = NhanhClientBuilder::create()
    ->withAppId('your_app_id')
    ->withBusinessId('your_business_id')
    ->withAccessToken('your_access_token')
    ->forDevelopment()
    ->build();
```

### 4. Tạo client cho production

```php
$client = NhanhClientBuilder::create()
    ->withAppId('your_app_id')
    ->withBusinessId('your_business_id')
    ->withAccessToken('your_access_token')
    ->forProduction()
    ->build();
```

## 🔧 Các phương thức cấu hình

### Configuration Methods

| Method | Mô tả | Ví dụ |
|--------|-------|-------|
| `withAppId(string $appId)` | Thiết lập App ID | `->withAppId('app123')` |
| `withBusinessId(string $businessId)` | Thiết lập Business ID | `->withBusinessId('biz456')` |
| `withAccessToken(string $accessToken)` | Thiết lập Access Token | `->withAccessToken('token789')` |
| `withSecretKey(string $secretKey)` | Thiết lập Secret Key (OAuth) | `->withSecretKey('secret123')` |
| `withRedirectUrl(string $redirectUrl)` | Thiết lập Redirect URL (OAuth) | `->withRedirectUrl('https://app.com/callback')` |
| `withApiVersion(string $apiVersion)` | Thiết lập API Version | `->withApiVersion('2.0')` |
| `withApiDomain(string $apiDomain)` | Thiết lập API Domain | `->withApiDomain('https://api.nhanh.vn')` |
| `withTimeout(int $timeout)` | Thiết lập Timeout (giây) | `->withTimeout(60)` |
| `withRetryAttempts(int $attempts)` | Thiết lập số lần retry | `->withRetryAttempts(5)` |
| `withRateLimit(int $limit)` | Thiết lập rate limit | `->withRateLimit(200)` |
| `withEnvironment(string $env)` | Thiết lập environment | `->withEnvironment('staging')` |
| `withSSLValidation(bool $validate)` | Thiết lập SSL validation | `->withSSLValidation(false)` |

### Logging Methods

| Method | Mô tả | Ví dụ |
|--------|-------|-------|
| `withLogger()` | Bật logging | `->withLogger()` |
| `withLogLevel(string $level)` | Thiết lập log level | `->withLogLevel('DEBUG')` |
| `withLogFile(string $file)` | Thiết lập log file | `->withLogFile('logs/app.log')` |
| `withConsoleLogging()` | Bật log ra console | `->withConsoleLogging()` |
| `withFileLogging()` | Bật log ra file | `->withFileLogging()` |
| `withLogRotation(int $days)` | Thiết lập log rotation | `->withLogRotation(7)` |

### Environment Presets

| Method | Mô tả | Cấu hình tự động |
|--------|-------|------------------|
| `forDevelopment()` | Cấu hình cho development | Environment: development, Logging: DEBUG, Console: true, SSL: false |
| `forProduction()` | Cấu hình cho production | Environment: production, Logging: WARNING, File: true, SSL: true |
| `forTesting()` | Cấu hình cho testing | Environment: testing, Logging: DEBUG, Console: true, SSL: false |

## 🚀 Static Convenience Methods

### 1. Tạo client cơ bản

```php
$client = NhanhClientBuilder::createBasic(
    'your_app_id',
    'your_business_id',
    'your_access_token'
);
```

### 2. Tạo client với OAuth

```php
$client = NhanhClientBuilder::createOAuth(
    'your_app_id',
    'your_secret_key',
    'https://your-app.com/callback'
);
```

### 3. Tạo client cho development

```php
$client = NhanhClientBuilder::createDevelopment(
    'your_app_id',
    'your_business_id',
    'your_access_token'
);
```

### 4. Tạo client cho production

```php
$client = NhanhClientBuilder::createProduction(
    'your_app_id',
    'your_business_id',
    'your_access_token'
);
```

## 📁 Tạo client từ file config

### 1. Tạo file config JSON

```json
{
    "appId": "your_app_id",
    "businessId": "your_business_id",
    "accessToken": "your_access_token",
    "apiVersion": "2.0",
    "timeout": 30,
    "enableLogging": true,
    "logLevel": "INFO",
    "logFile": "logs/nhanh.log",
    "environment": "production"
}
```

### 2. Sử dụng trong code

```php
$client = NhanhClientBuilder::fromConfigFile('config/nhanh.json')->build();
```

## 🌍 Tạo client từ environment variables

### 1. Thiết lập environment variables

```bash
# .env file
NHANH_APP_ID=your_app_id
NHANH_BUSINESS_ID=your_business_id
NHANH_ACCESS_TOKEN=your_access_token
NHANH_SECRET_KEY=your_secret_key
NHANH_API_VERSION=2.0
NHANH_ENVIRONMENT=production
NHANH_TIMEOUT=30
NHANH_LOG_LEVEL=INFO
```

### 2. Sử dụng trong code

```php
$client = NhanhClientBuilder::fromEnvironment()->build();
```

## 🔐 OAuth Flow

### 1. Tạo client cho OAuth

```php
$client = NhanhClientBuilder::fromOAuth()
    ->withAppId('your_app_id')
    ->withSecretKey('your_secret_key')
    ->withRedirectUrl('https://your-app.com/callback')
    ->build();
```

### 2. Lấy OAuth URL

```php
$oauthUrl = $client->getOAuthUrl('https://your-app.com/callback');
```

### 3. Xử lý callback

```php
$accessCode = $_GET['access_code'] ?? null;
if ($accessCode) {
    $token = $client->exchangeAccessCode($accessCode);
    // Lưu token vào database
}
```

## 📝 Logging Configuration

### 1. Logging cơ bản

```php
$client = NhanhClientBuilder::create()
    ->withAppId('your_app_id')
    ->withBusinessId('your_business_id')
    ->withAccessToken('your_access_token')
    ->withLogger()
    ->build();
```

### 2. Logging với tùy chọn chi tiết

```php
$client = NhanhClientBuilder::create()
    ->withAppId('your_app_id')
    ->withBusinessId('your_business_id')
    ->withAccessToken('your_access_token')
    ->withLogger()
    ->withLogLevel('DEBUG')
    ->withLogFile('logs/nhanh-debug.log')
    ->withConsoleLogging()
    ->withFileLogging()
    ->withLogRotation(7)
    ->build();
```

### 3. Log levels có sẵn

- `DEBUG` - Thông tin chi tiết cho development
- `INFO` - Thông tin chung
- `WARNING` - Cảnh báo
- `ERROR` - Lỗi
- `CRITICAL` - Lỗi nghiêm trọng

## 🎯 Các use cases phổ biến

### 1. Development Environment

```php
$client = NhanhClientBuilder::create()
    ->withAppId('dev_app_id')
    ->withBusinessId('dev_business_id')
    ->withAccessToken('dev_access_token')
    ->forDevelopment()
    ->build();
```

### 2. Production Environment

```php
$client = NhanhClientBuilder::create()
    ->withAppId('prod_app_id')
    ->withBusinessId('prod_business_id')
    ->withAccessToken('prod_access_token')
    ->forProduction()
    ->build();
```

### 3. Testing Environment

```php
$client = NhanhClientBuilder::create()
    ->withAppId('test_app_id')
    ->withBusinessId('test_business_id')
    ->withAccessToken('test_access_token')
    ->forTesting()
    ->build();
```

### 4. Custom Configuration

```php
$client = NhanhClientBuilder::create()
    ->withAppId('custom_app_id')
    ->withBusinessId('custom_business_id')
    ->withAccessToken('custom_access_token')
    ->withApiVersion('2.1')
    ->withTimeout(60)
    ->withRetryAttempts(5)
    ->withRateLimit(300)
    ->withLogger()
    ->withLogLevel('INFO')
    ->withLogFile('logs/custom.log')
    ->withConsoleLogging()
    ->build();
```

## ⚠️ Validation và Error Handling

### 1. Validation tự động

Builder sẽ tự động validate configuration trước khi build:

- **App ID** phải được cung cấp
- **Business ID** và **Access Token** phải được cung cấp (trừ OAuth flow)
- **Secret Key** và **Redirect URL** phải được cung cấp cho OAuth flow

### 2. Error messages

```php
try {
    $client = NhanhClientBuilder::create()
        ->withAppId('app_id')
        // Thiếu businessId và accessToken
        ->build();
} catch (Exception $e) {
    echo $e->getMessage();
    // Output: Configuration không hợp lệ:
    // Business ID không được để trống
    // Access Token không được để trống
}
```

## 🔄 Migration từ code cũ

### Code cũ (ClientConfig)

```php
use Puleeno\NhanhVn\Config\ClientConfig;
use Puleeno\NhanhVn\Client\NhanhVnClient;

$config = new ClientConfig([
    'appId' => 'your_app_id',
    'businessId' => 'your_business_id',
    'accessToken' => 'your_access_token',
    'timeout' => 30
]);

$client = NhanhVnClient::getInstance($config);
```

### Code mới (NhanhClientBuilder)

```php
use Puleeno\NhanhVn\Client\NhanhClientBuilder;

$client = NhanhClientBuilder::create()
    ->withAppId('your_app_id')
    ->withBusinessId('your_business_id')
    ->withAccessToken('your_access_token')
    ->withTimeout(30)
    ->build();
```

## 📚 Best Practices

### 1. Sử dụng environment presets

```php
// ✅ Tốt - Sử dụng preset
$client = NhanhClientBuilder::create()
    ->withAppId('your_app_id')
    ->withBusinessId('your_business_id')
    ->withAccessToken('your_access_token')
    ->forDevelopment()
    ->build();

// ❌ Không tốt - Thiết lập thủ công
$client = NhanhClientBuilder::create()
    ->withAppId('your_app_id')
    ->withBusinessId('your_business_id')
    ->withAccessToken('your_access_token')
    ->withEnvironment('development')
    ->withLogger()
    ->withLogLevel('DEBUG')
    ->withConsoleLogging()
    ->withSSLValidation(false)
    ->build();
```

### 2. Sử dụng static convenience methods

```php
// ✅ Tốt - Sử dụng convenience method
$client = NhanhClientBuilder::createBasic(
    'your_app_id',
    'your_business_id',
    'your_access_token'
);

// ❌ Không tốt - Builder pattern cho config đơn giản
$client = NhanhClientBuilder::create()
    ->withAppId('your_app_id')
    ->withBusinessId('your_business_id')
    ->withAccessToken('your_access_token')
    ->build();
```

### 3. Sử dụng file config cho production

```php
// ✅ Tốt - Sử dụng file config
$client = NhanhClientBuilder::fromConfigFile('config/nhanh.json')->build();

// ❌ Không tốt - Hardcode trong code
$client = NhanhClientBuilder::create()
    ->withAppId('prod_app_id')
    ->withBusinessId('prod_business_id')
    ->withAccessToken('prod_access_token')
    ->build();
```

## 🧪 Testing

### 1. Unit Testing

```php
use PHPUnit\Framework\TestCase;

class NhanhClientBuilderTest extends TestCase
{
    public function testCreateBasicClient()
    {
        $client = NhanhClientBuilder::createBasic(
            'test_app_id',
            'test_business_id',
            'test_access_token'
        );

        $this->assertInstanceOf(NhanhVnClient::class, $client);
        $this->assertTrue($client->isConfigured());
    }

    public function testInvalidConfiguration()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Business ID không được để trống');

        NhanhClientBuilder::create()
            ->withAppId('test_app_id')
            ->build();
    }
}
```

### 2. Integration Testing

```php
public function testClientWithLogger()
{
    $client = NhanhClientBuilder::create()
        ->withAppId('test_app_id')
        ->withBusinessId('test_business_id')
        ->withAccessToken('test_access_token')
        ->withLogger()
        ->withLogLevel('DEBUG')
        ->withConsoleLogging()
        ->build();

    $this->assertInstanceOf(MonologAdapter::class, $client->getLogger());
}
```

## 🔍 Troubleshooting

### 1. Lỗi "Configuration không hợp lệ"

**Nguyên nhân**: Thiếu các tham số bắt buộc
**Giải pháp**: Kiểm tra và cung cấp đầy đủ:
- `appId`
- `businessId` (nếu không phải OAuth)
- `accessToken` (nếu không phải OAuth)
- `secretKey` và `redirectUrl` (nếu là OAuth)

### 2. Lỗi "Config file không tồn tại"

**Nguyên nhân**: Đường dẫn file config không chính xác
**Giải pháp**: Kiểm tra đường dẫn file và quyền truy cập

### 3. Lỗi "Config file JSON không hợp lệ"

**Nguyên nhân**: File JSON có syntax error
**Giải pháp**: Validate JSON syntax bằng tool online hoặc IDE

### 4. Lỗi "Log directory không thể tạo"

**Nguyên nhân**: Không có quyền tạo thư mục
**Giải pháp**: Kiểm tra quyền write của thư mục logs

## 📖 API Reference

### Class: `NhanhClientBuilder`

#### Static Methods

| Method | Return Type | Mô tả |
|--------|-------------|-------|
| `create()` | `self` | Tạo builder instance mới |
| `fromConfigFile(string $path)` | `self` | Tạo builder từ file config |
| `fromEnvironment()` | `self` | Tạo builder từ environment variables |
| `fromOAuth()` | `self` | Tạo builder cho OAuth flow |
| `createBasic(string, string, string)` | `NhanhVnClient` | Tạo client cơ bản |
| `createOAuth(string, string, string)` | `NhanhVnClient` | Tạo client OAuth |
| `createDevelopment(string, string, string)` | `NhanhVnClient` | Tạo client development |
| `createProduction(string, string, string)` | `NhanhVnClient` | Tạo client production |

#### Instance Methods

| Method | Return Type | Mô tả |
|--------|-------------|-------|
| `withAppId(string)` | `self` | Thiết lập App ID |
| `withBusinessId(string)` | `self` | Thiết lập Business ID |
| `withAccessToken(string)` | `self` | Thiết lập Access Token |
| `withLogger()` | `self` | Bật logging |
| `forDevelopment()` | `self` | Cấu hình development |
| `forProduction()` | `self` | Cấu hình production |
| `build()` | `NhanhVnClient` | Build và return client |

## 🎉 Kết luận

**Nhanh Client Builder** cung cấp một cách tiếp cận hiện đại và trực quan để tạo `NhanhVnClient`. Với fluent interface, environment presets, và static convenience methods, developer có thể setup client một cách nhanh chóng và dễ dàng.

Builder pattern này giúp code dễ đọc, dễ maintain và giảm thiểu lỗi configuration. Đặc biệt hữu ích cho các dự án cần nhiều environment khác nhau hoặc cần logging configuration phức tạp.
