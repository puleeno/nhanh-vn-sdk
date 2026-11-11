# Nhanh.vn SDK - Tài liệu (API v3)

SDK hỗ trợ **Nhanh.vn Open API v3** (`apiVersion=3.0` mặc định).

## Tham chiếu nhanh

- **Khởi tạo:** `NhanhClientBuilder::create()->withAppId()->withBusinessId()->withAccessToken()->build()` (hoặc `fromEnvironment()`, `fromOAuth()`).
- **OAuth v3:** `getOAuthUrl($returnLink, $verifyCode, 'pos'|'ship')` → callback `accessCode` + `verifyCode` → `exchangeAccessCode($accessCode, $verifyCode)`.
- **Gọi API v3:** `POST /v3.0/<resource>/<action>?appId&businessId`, header `Authorization: <accessToken>`, body JSON `{filters,paginator,dataOptions}`.
- **Generic:** `$client->getHttpService()->callApi('/v3.0/...', $payload)` cho mọi endpoint theo spec.

## Tài liệu

- [README chính](../README.md) — cài đặt, OAuth v3, ví dụ modules, lưu ý.
- [UPGRADE](../UPGRADE.md) — nâng cấp lên 1.0.0/v3.
- [CHANGELOG](../CHANGELOG.md) — lịch sử thay đổi.
- [Client Builder](client-builder.md), [Quickstart](client-builder-quickstart.md) — tạo client.

## Spec Nhanh v3 (tham chiếu ngoài SDK)

- `modules/NhanhApiReplacement/specs/common/` — overview, access-token, api-requests.
- `modules/NhanhApiReplacement/specs/pos/*` — product, order, customer, shipping, inventory, bill, ...

## Trạng thái module

| Module | v3 endpoints đã wrap |
|---|---|
| OAuth | authorize URL, `/v3.0/app/getaccesstoken` |
| Product | `/v3.0/product/list`, `detail`, `add`, `category` |
| Order | `/v3.0/order/list`, `add`, `edit` |
| Customer | `/v3.0/customer/list`, `add` |
| Shipping | `/v3.0/shipping/location`, `carrier`, `fee` |

Các endpoint khác: gọi qua `HttpService::callApi()`. Entities/managers/services legacy có thể cần map thêm theo response v3.
