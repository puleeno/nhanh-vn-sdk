# Nhanh.vn PHP SDK (API v3)

[![PHP Version](https://img.shields.io/badge/php-8.1%2B-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Version](https://img.shields.io/badge/version-1.0.0-orange.svg)](https://github.com/puleeno/nhanh-vn-sdk/releases)

PHP SDK tích hợp **Nhanh.vn Open API v3** (POS/Vpage/WebApp), hỗ trợ OAuth v3 (`service`, `verifyCode`) và gọi API JSON theo spec v3 (`/v3.0/...`, `appId`/`businessId` query, `Authorization: <accessToken>`).

> **Lưu ý:** Core transport và các module chính đã migrate sang v3. Nhiều endpoint POS khác trong spec có thể gọi trực tiếp qua `HttpService::callApi('/v3.0/...', $payload)`; một số entity/manager/service legacy (v2) vẫn tồn tại và có thể cần map thêm tùy luồng.

## Yêu cầu

- PHP >= 8.1
- Composer 2.0+
- Extensions: `curl`, `json`, `mbstring`, `openssl`

## Cài đặt

```bash
composer require puleeno/nhanh-vn-sdk
```

## Khởi tạo client (v3)

```php
use Puleeno\NhanhVn\Client\NhanhClientBuilder;

// API calls (cần accessToken + businessId)
$client = NhanhClientBuilder::create()
    ->withAppId('YOUR_APP_ID')
    ->withBusinessId('YOUR_BUSINESS_ID')
    ->withAccessToken('YOUR_ACCESS_TOKEN')
    ->withApiVersion('3.0') // mặc định
    ->build();

// Hoặc từ biến môi trường:
// NHANH_APP_ID, NHANH_BUSINESS_ID, NHANH_ACCESS_TOKEN, NHANH_API_VERSION (mặc định 3.0)
$client = NhanhClientBuilder::fromEnvironment()->build();
```

`ClientConfig` mặc định: `apiDomain=https://pos.open.nhanh.vn`, `apiVersion=3.0`.

## OAuth v3 (lấy access token)

Spec: `https://nhanh.vn/oauth?appId=...&service=pos&verifyCode=...&returnLink=...`  
`returnLink` phải HTTPS và **khớp chính xác** Redirect URL đã đăng ký trên app Nhanh. `verifyCode` 12–64 ký tự `[a-zA-Z0-9]` (tự sinh, lưu lại để đối chiếu khi callback và gửi khi đổi token).

```php
use Puleeno\NhanhVn\Client\NhanhClientBuilder;

$client = NhanhClientBuilder::fromOAuth()
    ->withAppId('YOUR_APP_ID')
    ->withSecretKey('YOUR_SECRET_KEY')
    ->withReturnLink('https://your-app.com/nhanh/callback') // hoặc withRedirectUrl(...)
    ->withApiVersion('3.0')
    ->build();

// 1) Tạo verifyCode, lưu session/transient, redirect user
$verifyCode = bin2hex(random_bytes(16)); // ví dụ 32 ký tự
$authUrl = $client->getOAuthUrl('https://your-app.com/nhanh/callback', $verifyCode, 'pos');
// redirect($authUrl);

// 2) Callback nhận accessCode (+ verifyCode) -> đối chiếu verifyCode -> đổi token
$token = $client->exchangeAccessCode($_GET['accessCode'], $verifyCode);
// $token['accessToken'], $token['businessId'], $token['expiredAt'], $token['permissions'], ...
```

Đổi token gọi `POST https://pos.open.nhanh.vn/v3.0/app/getaccesstoken?appId=...` (header `Authorization: <secretKey>`, body JSON `{accessCode, verifyCode}`).

## Gọi API v3

Mọi request v3: `POST /v3.0/<resource>/<action>?appId=...&businessId=...`  
Headers: `Authorization: <accessToken>`, `Content-Type: application/json`  
Body: JSON (ví dụ `{filters, paginator, dataOptions}` hoặc mảng object tùy API).

```php
// Generic (bất kỳ endpoint v3 trong spec)
$res = $client->getHttpService()->callApi('/v3.0/product/list', [
    'filters' => ['name' => 'Ao so mi'],
    'paginator' => ['size' => 50],
]);
if ($res['code'] === 1) {
    $items = $res['data'];           // thường là mảng
    $next  = $res['paginator']['next'] ?? null;
}
```

## Modules hỗ trợ v3

| Module | Endpoint v3 | Ghi chú |
|---|---|---|
| OAuth | `/oauth` (authorize), `/v3.0/app/getaccesstoken` | `service`, `verifyCode` |
| Product | `/v3.0/product/list`, `/detail`, `/add`, `/category` | `search()` map v3 `data[]`, `getCategories(filters,paginator)` |
| Order | `/v3.0/order/list`, `/add`, `/edit` | list normalize `data[]` + `paginator.next` |
| Customer | `/v3.0/customer/list`, `/add` | list filters (`ids/mobile/type/updatedAtFrom..`), add body là mảng (max 100) |
| Shipping | `/v3.0/shipping/location`, `/carrier`, `/fee` | location `filters{locationVersion,type,parentId}`, fee `type` 1/2 |

### Ví dụ Product

```php
$products = $client->products()->search([
    'filters' => ['name' => 'iPhone', 'status' => [1]],
    'paginator' => ['size' => 50],
]);
$categories = $client->products()->getCategories(['status' => 1], ['size' => 50]);
$detail = $client->products()->detail(123);
```

### Ví dụ Customer

```php
$customers = $client->customers()->search([
    'filters' => ['type' => 1, 'mobile' => '0988999999'],
    'paginator' => ['size' => 10],
]);
// hoặc thêm mới (v3 body = array)
$resp = $client->customers()->add([
    ['name' => 'Nguyen Van A', 'mobile' => '0989999998'],
]);
```

### Ví dụ Shipping

```php
$cities = $client->shipping()->searchCities('v1');
$districts = $client->shipping()->searchDistricts(254, 'v1');
$wards = $client->shipping()->searchWards(331, 'v1');
$carriers = $client->shipping()->getCarriers();

// Tính phí (v3: /v3.0/shipping/fee, filters.type 1|2)
$fee = $client->shipping()->calculateFeeFromArray([
    'filters' => [
        'type' => 1,
        'shippingWeight' => 2100,
        'shippingFrom' => ['cityId' => 254, 'districtId' => 331, 'wardId' => 1026, 'locationVersion' => 'v1'],
        'shippingTo' => ['cityId' => 254, 'districtId' => 331, 'wardId' => 1120, 'locationVersion' => 'v1'],
    ],
]);
```

### Ví dụ Order

```php
$orders = $client->orders()->search(['filters' => [], 'paginator' => ['size' => 50]]);
```

## Lỗi & rate limit

- Response thành công: `code=1` (+ `data`, `paginator.next`, `warning` tùy API).
- Lỗi: `code=0`, `errorCode` (vd `ERR_403`, `ERR_429`, `ERR_INVALID_DEPOT_ID`), `messages`.
- `HttpService` ném `ApiException` (kèm `errorCode`) / `NetworkException`. Xem `common/3.api-requests.md` để xử lý phân trang và rate limit (150 req/30s).

## Cấu trúc dự án

```
src/
├── Client/       # NhanhVnClient, NhanhClientBuilder
├── Config/       # ClientConfig (v3: apiVersion, oauthService, verifyCode)
├── Contracts/    # Interfaces
├── Entities/     # Data entities
├── Exceptions/   # ApiException, NetworkException, ...
├── Managers/     # Business logic
├── Modules/      # Product, Order, Customer, Shipping, OAuth
├── Repositories/ # Data access / factories
└── Services/     # HttpService (v3), OAuthService, Cache, Logger, ...
```

## Testing

```bash
composer install
./vendor/bin/phpunit
```

Unit tests (v3): `tests/Unit/{Config,Services,Modules,Client}` (OAuth URL/verifyCode, HttpService v3 query/header/json, token exchange, endpoint/payload modules).

## Lưu ý tương thích

- `apiVersion` mặc định **3.0** (`NhanhClientBuilder::fromEnvironment()` cũng mặc định 3.0).
- SDK sửa trong `vendor/` (khi nhúng trực tiếp) có thể bị `composer install/update` ghi đè — nên pin nguồn/branch v3.
- Một số API/field nâng cao của POS (inventory, bill, invoice, promotion, webhook handlers, ...) chưa có wrapper riêng: dùng `callApi('/v3.0/...', $payload)` theo spec tương ứng.
- Tài liệu spec tham chiếu: `modules/NhanhApiReplacement/specs` (common, pos/*).

## License

MIT
