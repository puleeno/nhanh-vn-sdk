# OAuth API Documentation

## 📋 Tổng quan

OAuth API cung cấp hệ thống xác thực và authorization để truy cập Nhanh.vn API. Flow này tuân theo chuẩn OAuth 2.0 với các bước cụ thể của Nhanh.vn.

## 🔐 OAuth Flow

### Bước 1: Lấy Access Code

#### Endpoint
```
GET /oauth
```

#### Parameters
| Parameter | Type | Required | Mô tả |
|-----------|------|----------|-------|
| `version` | string | Yes | Phiên bản API (2.0) |
| `appId` | int | Yes | ID ứng dụng của bạn |
| `returnLink` | string | Yes | URL callback sau khi user authorize |

#### Ví dụ URL
```
https://nhanh.vn/oauth?version=2.0&appId=12345&returnLink=https://your-app.com/callback
```

#### Flow
1. User truy cập URL OAuth
2. User đăng nhập vào Nhanh.vn
3. User chọn quyền cho ứng dụng
4. Nhanh.vn redirect về `returnLink` với `accessCode`

#### Response
```
https://your-app.com/callback?accessCode=ABC123&state=xyz
```

### Bước 2: Lấy Access Token

#### Endpoint
```
POST /api/oauth/access_token
```

#### Parameters
| Parameter | Type | Required | Mô tả |
|-----------|------|----------|-------|
| `version` | string | Yes | Phiên bản API (2.0) |
| `appId` | int | Yes | ID ứng dụng của bạn |
| `accessCode` | string | Yes | Access code từ bước 1 |
| `secretKey` | string | Yes | Secret key của ứng dụng |

#### Request Body
```json
{
    "version": "2.0",
    "appId": 12345,
    "accessCode": "ABC123",
    "secretKey": "your-secret-key"
}
```

#### Response
```json
{
    "code": 1,
    "accessToken": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
    "expiredAt": "2024-12-31 23:59:59",
    "expiredDateTime": "2024-12-31 23:59:59",
    "businessId": 67890,
    "depotIds": [1, 2, 3],
    "permissions": "Sản phẩm, đơn hàng, khách hàng"
}
```

## 🏗️ Architecture

### OAuthModule Class

```php
class OAuthModule
{
    /**
     * Lấy URL OAuth để user authorize
     * 
     * @return string URL OAuth hoàn chỉnh
     */
    public function getOAuthUrl(): string;

    /**
     * Lấy access token từ access code
     * 
     * @param string $accessCode Access code từ OAuth callback
     * @return array Thông tin tokens và permissions
     * @throws Exception Khi có lỗi xảy ra
     */
    public function getAccessToken(string $accessCode): array;
}
```

### OAuthService Class

```php
class OAuthService
{
    /**
     * Tạo URL OAuth với các parameters cần thiết
     * 
     * @param string $redirectUrl URL callback
     * @return string URL OAuth hoàn chỉnh
     */
    public function createOAuthUrl(string $redirectUrl): string;

    /**
     * Gọi API để lấy access token
     * 
     * @param string $accessCode Access code từ callback
     * @return array Response từ API
     * @throws Exception Khi có lỗi xảy ra
     */
    public function exchangeAccessToken(string $accessCode): array;
}
```

## 💻 Sử dụng trong code

### Khởi tạo OAuth flow
```php
use Puleeno\NhanhVn\Client\NhanhVnClient;

$client = NhanhVnClient::getInstance($config);

// Lấy URL OAuth
$oauthUrl = $client->oauth()->getOAuthUrl();
echo "Truy cập: " . $oauthUrl;
```

### Xử lý callback
```php
// Trong callback handler
$accessCode = $_GET['accessCode'] ?? null;

if ($accessCode) {
    try {
        $tokens = $client->oauth()->getAccessToken($accessCode);
        
        // Lưu tokens
        $accessToken = $tokens['accessToken'];
        $businessId = $tokens['businessId'];
        $expiredAt = $tokens['expiredAt'];
        $permissions = $tokens['permissions'];
        $depotIds = $tokens['depotIds'];
        
        // Cập nhật config
        $client->getConfig()->setAccessToken($accessToken);
        $client->getConfig()->setBusinessId($businessId);
        
        echo "OAuth thành công!";
    } catch (Exception $e) {
        echo "Lỗi OAuth: " . $e->getMessage();
    }
}
```

## 🔧 Cấu hình

### ClientConfig requirements
```php
// Để lấy OAuth URL
$config = new ClientConfig([
    'appId' => 'YOUR_APP_ID',
    'secretKey' => 'YOUR_SECRET_KEY',
    'redirectUrl' => 'https://your-app.com/callback'
]);

// Để gọi API (sau khi có access token)
$config = new ClientConfig([
    'appId' => 'YOUR_APP_ID',
    'businessId' => 'BUSINESS_ID_FROM_OAUTH',
    'accessToken' => 'ACCESS_TOKEN_FROM_OAUTH',
    'version' => '2.0'
]);
```

### Environment variables
```bash
# .env
NHANH_APP_ID=12345
NHANH_SECRET_KEY=your-secret-key
NHANH_REDIRECT_URL=https://your-app.com/callback
NHANH_API_VERSION=2.0
NHANH_API_DOMAIN=https://pos.open.nhanh.vn
```

## 🚨 Error Handling

### OAuth Errors
```php
try {
    $tokens = $client->oauth()->getAccessToken($accessCode);
} catch (ApiException $e) {
    if ($e->getErrorCode() === 'INVALID_ACCESS_CODE') {
        echo "Access code không hợp lệ hoặc đã hết hạn";
    } elseif ($e->getErrorCode() === 'INVALID_APP_CREDENTIALS') {
        echo "App ID hoặc Secret Key không đúng";
    } else {
        echo "Lỗi OAuth: " . $e->getMessage();
    }
} catch (Exception $e) {
    echo "Lỗi chung: " . $e->getMessage();
}
```

### Common Error Codes
| Error Code | Mô tả | Giải pháp |
|------------|-------|-----------|
| `INVALID_ACCESS_CODE` | Access code không hợp lệ | Yêu cầu user authorize lại |
| `EXPIRED_ACCESS_CODE` | Access code đã hết hạn | Yêu cầu user authorize lại |
| `INVALID_APP_CREDENTIALS` | App ID/Secret không đúng | Kiểm tra cấu hình |
| `INSUFFICIENT_PERMISSIONS` | Không đủ quyền | Kiểm tra quyền trong Nhanh.vn |

## 🔄 Token Management

### Token Expiration
```php
// Kiểm tra token có hết hạn không
$expiredAt = $tokens['expiredAt'];
$expiredDateTime = new DateTime($expiredAt);
$now = new DateTime();

if ($now >= $expiredDateTime) {
    // Token đã hết hạn, yêu cầu refresh
    echo "Token đã hết hạn, vui lòng authorize lại";
}
```

### Token Storage
```php
// Lưu tokens vào database hoặc cache
$tokenData = [
    'accessToken' => $tokens['accessToken'],
    'businessId' => $tokens['businessId'],
    'expiredAt' => $tokens['expiredAt'],
    'permissions' => $tokens['permissions'],
    'depotIds' => $tokens['depotIds'],
    'createdAt' => date('Y-m-d H:i:s')
];

// Lưu vào database
$db->insert('oauth_tokens', $tokenData);

// Hoặc lưu vào cache
$cache->set('nhanh_oauth_tokens', $tokenData, 3600);
```

### Token Refresh
```php
// Kiểm tra và refresh token khi cần
public function ensureValidToken(): bool
{
    $tokens = $this->getStoredTokens();
    
    if (!$tokens) {
        return $this->initiateOAuthFlow();
    }
    
    $expiredAt = new DateTime($tokens['expiredAt']);
    $now = new DateTime();
    
    // Refresh nếu còn 1 giờ nữa hết hạn
    if ($now >= $expiredAt->modify('-1 hour')) {
        return $this->initiateOAuthFlow();
    }
    
    return true;
}
```

## 🔍 Logging

### OAuth Logging
```php
// Cấu hình logger
$client->setLogger($monologAdapter);

// Logs sẽ được ghi tự động:
// - OAuth URL generation
// - Access token exchange
// - Token validation
// - Error responses
```

### Log Examples
```php
// INFO: OAuth URL generated
$logger->info('OAuth URL generated', [
    'appId' => $config->getAppId(),
    'redirectUrl' => $config->getRedirectUrl()
]);

// INFO: Access token exchange successful
$logger->info('Access token exchange successful', [
    'businessId' => $tokens['businessId'],
    'expiredAt' => $tokens['expiredAt']
]);

// ERROR: OAuth exchange failed
$logger->error('OAuth exchange failed', [
    'errorCode' => $e->getErrorCode(),
    'errorMessage' => $e->getMessage()
]);
```

## 💡 Best Practices

### 1. Secure Token Storage
```php
// Sử dụng encryption cho sensitive data
$encryptedToken = encrypt($tokens['accessToken'], $encryptionKey);
$db->insert('oauth_tokens', ['encrypted_token' => $encryptedToken]);
```

### 2. Token Validation
```php
// Luôn validate token trước khi sử dụng
public function validateToken(string $accessToken): bool
{
    try {
        // Gọi API test để validate token
        $response = $this->httpService->callApi('/test', []);
        return $response['code'] === 1;
    } catch (Exception $e) {
        return false;
    }
}
```

### 3. Graceful Degradation
```php
// Xử lý trường hợp OAuth thất bại
try {
    $tokens = $client->oauth()->getAccessToken($accessCode);
    $this->processWithTokens($tokens);
} catch (Exception $e) {
    // Fallback to cached data hoặc offline mode
    $this->fallbackToOfflineMode();
}
```

### 4. User Experience
```php
// Cung cấp clear instructions cho user
echo "Để sử dụng tính năng này, bạn cần:";
echo "1. Truy cập: " . $oauthUrl;
echo "2. Đăng nhập vào Nhanh.vn";
echo "3. Chọn quyền cho ứng dụng";
echo "4. Quay lại trang này";
```

## 🔒 Security Considerations

### HTTPS Only
- Luôn sử dụng HTTPS cho OAuth flow
- Không bao giờ gửi tokens qua HTTP

### Token Protection
- Không log access tokens
- Sử dụng secure storage (database với encryption)
- Implement token rotation nếu có thể

### Scope Limitation
- Chỉ request permissions cần thiết
- Giải thích rõ ràng cho user về quyền được yêu cầu

## 📚 Related Documentation

- [Product API](product.md) - Sử dụng access token để gọi API
- [Error Handling](errors.md) - Xử lý lỗi OAuth
- [Configuration](configuration.md) - Cấu hình OAuth
- [Security](security.md) - Bảo mật OAuth flow
