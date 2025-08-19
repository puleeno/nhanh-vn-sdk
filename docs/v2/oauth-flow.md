# OAuth Flow với Nhanh.vn SDK v2.0

## Tổng quan

**⚠️ Lưu ý quan trọng:** Nhanh.vn API 2.0 **KHÔNG sử dụng OAuth 2.0 chuẩn** mà là flow xác thực riêng của họ. Tên "OAuth" ở đây chỉ là tên gọi, không phải protocol OAuth 2.0 chuẩn.

## Flow xác thực thực tế

### 1. Tạo URL xác thực
```php
use Puleeno\NhanhVn\Client\NhanhVnClient;
use Puleeno\NhanhVn\Config\ClientConfig;

// Tạo config cơ bản
$config = new ClientConfig([
    'appId' => 'YOUR_APP_ID',
    'secretKey' => 'YOUR_SECRET_KEY',
    'apiVersion' => '2.0',
    'baseUrl' => 'https://pos.open.nhanh.vn'
]);

// Khởi tạo client
$client = NhanhVnClient::getInstance($config);

// Tạo URL xác thực
$oauthUrl = $client->getOAuthUrl('https://your-domain.com/callback');
echo "OAuth URL: " . $oauthUrl;
```

### 2. User authorize
User truy cập URL xác thực và cấp quyền cho ứng dụng.

### 3. Nhận access_code
Nhanh.vn redirect user về callback URL với `accessCode` parameter:

```php
// Trong callback.php
$accessCode = $_GET['accessCode'] ?? null;
if (!$accessCode) {
    die('Không nhận được accessCode');
}
```

### 4. Đổi access_code lấy access_token
```php
try {
    // Tạo config tạm thời để khởi tạo client
    $tempConfig = new ClientConfig([
        'appId' => 'YOUR_APP_ID',
        'secretKey' => 'YOUR_SECRET_KEY',
        'apiVersion' => '2.0',
        'baseUrl' => 'https://pos.open.nhanh.vn'
    ]);

    // Khởi tạo client tạm thời
    $tempClient = NhanhVnClient::getInstance($tempConfig);

    // Sử dụng OAuth service của SDK
    $result = $tempClient->oauth()->exchangeAccessCode($accessCode);

    if ($result['code'] === 1) {
        $accessToken = $result['data']['accessToken'] ?? $result['accessToken'];
        $businessId = $result['data']['businessId'] ?? $result['businessId'] ?? null;

        // Lưu token vào database hoặc file
        saveAccessToken($accessToken, $businessId);

        echo "Xác thực thành công!";
    } else {
        echo "Lỗi: " . $result['message'];
    }

} catch (Exception $e) {
    echo "Lỗi xác thực: " . $e->getMessage();
}
```

## Sử dụng OAuthExample (Khuyến nghị)

### 1. Tạo OAuthExample instance
```php
use Examples\OAuthExample;

$app = new OAuthExample();

// Hiển thị link xác thực
$app->showOAuthLink();

// Lấy URL xác thực
$oauthUrl = $app->getOAuthUrl();
```

### 2. Xử lý callback
```php
// Trong callback.php
$app = new OAuthExample();
$app->handleCallback();
```

### 3. Khởi tạo client với access token
```php
// Sử dụng boot file
require_once __DIR__ . '/boot/client.php';

// Khởi tạo client
$client = bootNhanhVnClientSilent();

// Kiểm tra client đã sẵn sàng
if (isClientReady()) {
    echo "Client đã sẵn sàng!";
} else {
    echo "Client chưa sẵn sàng. Vui lòng chạy flow xác thực trước!";
}
```

## Cách sử dụng SDK trực tiếp

### Tạo OAuth URL
```php
// Sử dụng NhanhVnClient trực tiếp
$client = NhanhVnClient::getInstance($config);
$oauthUrl = $client->getOAuthUrl($callbackUrl);

// Hoặc sử dụng OAuth module
$oauthUrl = $client->oauth()->getOAuthUrl($callbackUrl);
```

### Đổi Access Code
```php
// Sử dụng OAuth module
$result = $client->oauth()->exchangeAccessCode($accessCode);

// Hoặc sử dụng method trực tiếp của client
$result = $client->exchangeAccessCode($accessCode);
```

### Cấu hình cần thiết
```php
$config = new ClientConfig([
    'appId' => 'YOUR_APP_ID',
    'secretKey' => 'YOUR_SECRET_KEY',        // Cần thiết cho exchangeAccessCode
    'apiVersion' => '2.0',
    'baseUrl' => 'https://pos.open.nhanh.vn'
]);
```

**⚠️ Lưu ý:** Để sử dụng `exchangeAccessCode()`, bạn cần có `secretKey` trong config. `secretKey` không cần thiết cho `getOAuthUrl()`.

## Cấu trúc Response

### OAuth URL Response
```
https://nhanh.vn/oauth?version=2.0&appId=YOUR_APP_ID&returnLink=YOUR_CALLBACK_URL&businessId=YOUR_BUSINESS_ID
```

### Access Token Response
```json
{
    "code": 1,
    "message": "Thành công",
    "data": {
        "accessToken": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "businessId": "12345",
        "expiresIn": 2592000
    }
}
```

## Error Handling

### Các loại lỗi thường gặp
```php
try {
    $result = $client->oauth()->exchangeAccessCode($accessCode);

    // Kiểm tra response code
    if ($result['code'] !== 1) {
        $message = is_array($result['message'] ?? '')
            ? implode(', ', $result['message'])
            : ($result['message'] ?? 'Unknown error');
        throw new ApiException($message);
    }

    // Xử lý response thành công
    $accessToken = $result['data']['accessToken'] ?? $result['accessToken'];
    $businessId = $result['data']['businessId'] ?? $result['businessId'];

} catch (ConfigurationException $e) {
    // Lỗi cấu hình (thiếu appId, secretKey)
    echo "Lỗi cấu hình: " . $e->getMessage();
} catch (ApiException $e) {
    // Lỗi từ Nhanh.vn API
    echo "Lỗi API: " . $e->getMessage();
} catch (Exception $e) {
    // Lỗi khác
    echo "Lỗi: " . $e->getMessage();
}
```

### Retry Logic
```php
$maxRetries = 3;
$retryCount = 0;

while ($retryCount < $maxRetries) {
    try {
        $result = $client->oauth()->exchangeAccessCode($accessCode);

        if ($result['code'] === 1) {
            break; // Thành công
        } else {
            throw new ApiException($result['message']);
        }

    } catch (Exception $e) {
        $retryCount++;
        if ($retryCount >= $maxRetries) {
            throw $e; // Hết số lần thử
        }

        // Đợi trước khi thử lại
        sleep(2);
    }
}
```

### Debug Response
```php
try {
    $result = $client->oauth()->exchangeAccessCode($accessCode);

    // Debug: Log response để xem cấu trúc
    error_log("Nhanh.vn API Response: " . json_encode($result));

    // Hiển thị debug info
    echo '<pre>' . htmlspecialchars(json_encode($result, JSON_PRETTY_PRINT)) . '</pre>';

} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage();
}
```

## Best Practices

### 1. Sử dụng SDK methods
```php
// ĐÚNG - Sử dụng SDK
$oauthUrl = $client->getOAuthUrl($callbackUrl);
$result = $client->oauth()->exchangeAccessCode($accessCode);

// SAI - Tự implement
// $oauthUrl = 'https://nhanh.vn/oauth?...';
```

### 2. Error handling toàn diện
```php
try {
    $result = $client->oauth()->exchangeAccessCode($accessCode);

    // Kiểm tra response code
    if ($result['code'] !== 1) {
        $message = is_array($result['message'] ?? '')
            ? implode(', ', $result['message'])
            : ($result['message'] ?? 'Unknown error');
        throw new ApiException($message);
    }

    // Xử lý response thành công
    $accessToken = $result['data']['accessToken'] ?? $result['accessToken'];
    $businessId = $result['data']['businessId'] ?? $result['businessId'];

    return [
        'accessToken' => $accessToken,
        'businessId' => $businessId
    ];

} catch (Exception $e) {
    // Log lỗi
    error_log("OAuth Error: " . $e->getMessage());

    // Xử lý lỗi phù hợp
    throw $e;
}
```

### 3. Lưu trữ token an toàn
```php
// Lưu token vào database (khuyến nghị)
$tokens = [
    'access_token' => $accessToken,
    'business_id' => $businessId,
    'created_at' => date('Y-m-d H:i:s'),
    'expires_at' => date('Y-m-d H:i:s', strtotime('+30 days'))
];

// Hoặc lưu vào file (chỉ cho development)
file_put_contents('tokens.json', json_encode($tokens, JSON_PRETTY_PRINT));
```

### 4. Sử dụng OAuthExample cho development
```php
// Khuyến nghị sử dụng OAuthExample cho development
use Examples\OAuthExample;

$app = new OAuthExample();
$app->showOAuthLink();
$app->handleCallback();

// Sau đó sử dụng boot file
require_once __DIR__ . '/boot/client.php';
$client = bootNhanhVnClientSilent();
```

### 5. Cấu hình đúng cho từng method
```php
// Để tạo OAuth URL (không cần secretKey)
$configForUrl = new ClientConfig([
    'appId' => 'YOUR_APP_ID',
    'apiVersion' => '2.0',
    'baseUrl' => 'https://pos.open.nhanh.vn'
]);

// Để đổi access code (cần secretKey)
$configForExchange = new ClientConfig([
    'appId' => 'YOUR_APP_ID',
    'secretKey' => 'YOUR_SECRET_KEY',  // Bắt buộc
    'apiVersion' => '2.0',
    'baseUrl' => 'https://pos.open.nhanh.vn'
]);
```

## Troubleshooting

### Vấn đề thường gặp

1. **Không nhận được accessCode**
   - Kiểm tra callback URL có đúng không
   - Kiểm tra appId và secretKey
   - Kiểm tra user đã authorize chưa
   - Kiểm tra parameter name: Nhanh.vn sử dụng `accessCode` (không phải `access_code`)

2. **Lỗi khi đổi access code**
   - Kiểm tra accessCode có hợp lệ không
   - Kiểm tra appId và secretKey
   - Kiểm tra network connection
   - Kiểm tra response structure từ API

3. **Token hết hạn**
   - Implement refresh token logic
   - Hoặc chạy lại OAuth flow

4. **Lỗi cấu hình**
   - Thiếu `secretKey` khi gọi `exchangeAccessCode()`
   - Sai `baseUrl` (phải là `https://pos.open.nhanh.vn`)
   - Sai `apiVersion` (phải là `2.0`)

### Debug Mode
```php
// Bật debug logging
$client = bootNhanhVnClientWithLogger('DEBUG');

// Kiểm tra response
echo '<pre>' . print_r($result, true) . '</pre>';

// Log response vào file
error_log("OAuth Response: " . json_encode($result));
```

### Kiểm tra Response Structure
```php
try {
    $result = $client->oauth()->exchangeAccessCode($accessCode);

    // Debug response structure
    echo '<h3>🔍 Debug Response Structure:</h3>';
    echo '<pre>' . htmlspecialchars(json_encode($result, JSON_PRETTY_PRINT)) . '</pre>';

    // Kiểm tra các field có thể có
    echo '<h4>Response Fields:</h4>';
    echo '<ul>';
    echo '<li>code: ' . ($result['code'] ?? 'N/A') . '</li>';
    echo '<li>message: ' . (is_array($result['message'] ?? '') ? implode(', ', $result['message']) : ($result['message'] ?? 'N/A')) . '</li>';
    echo '<li>data: ' . (isset($result['data']) ? 'Present' : 'N/A') . '</li>';
    echo '<li>accessToken: ' . ($result['data']['accessToken'] ?? $result['accessToken'] ?? 'N/A') . '</li>';
    echo '<li>businessId: ' . ($result['data']['businessId'] ?? $result['businessId'] ?? 'N/A') . '</li>';
    echo '</ul>';

} catch (Exception $e) {
    echo '<h3>❌ Error:</h3>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
}
```

### Kiểm tra Config
```php
// Kiểm tra config trước khi sử dụng
$config = new ClientConfig([
    'appId' => 'YOUR_APP_ID',
    'secretKey' => 'YOUR_SECRET_KEY',
    'apiVersion' => '2.0',
    'baseUrl' => 'https://pos.open.nhanh.vn'
]);

echo '<h3>🔍 Config Check:</h3>';
echo '<ul>';
echo '<li>App ID: ' . $config->getAppId() . '</li>';
echo '<li>Secret Key: ' . (strlen($config->getSecretKey()) > 0 ? 'Set (' . strlen($config->getSecretKey()) . ' chars)' : 'Not set') . '</li>';
echo '<li>API Version: ' . $config->getApiVersion() . '</li>';
echo '<li>Base URL: ' . $config->getBaseUrl() . '</li>';
echo '</ul>';
```

## Liên kết

- [OAuthExample](../examples/src/OAuthExample.php) - Example implementation
- [NhanhVnClient](../src/Client/NhanhVnClient.php) - Main client class
- [OAuthModule](../src/Modules/OAuthModule.php) - OAuth module
- [OAuthService](../src/Services/OAuthService.php) - OAuth service
