# Upgrade Guide

Hướng dẫn nâng cấp Nhanh.vn PHP SDK từ các version cũ lên version mới nhất.

## 🔄 Từ Version 0.3.x lên 0.4.0

### ⚠️ Breaking Changes

#### 1. PHP Version Requirement
- **Từ:** PHP 7.4+
- **Lên:** PHP 8.1+ (khuyến nghị PHP 8.2+)

```bash
# Kiểm tra PHP version
php -v

# Nếu < 8.1, cần upgrade PHP
# Ubuntu/Debian
sudo apt update
sudo apt install php8.2 php8.2-curl php8.2-json php8.2-mbstring php8.2-openssl

# CentOS/RHEL
sudo yum install php82 php82-php-curl php82-php-json php82-php-mbstring php82-php-openssl
```

#### 2. Composer Version
- **Từ:** Composer 1.0+
- **Lên:** Composer 2.0+

```bash
# Kiểm tra Composer version
composer --version

# Upgrade Composer
composer self-update
```

#### 3. Namespace Changes
- **Từ:** `NhanhVn\Sdk\`
- **Lên:** `Puleeno\NhanhVn\`

```php
// Cũ
use NhanhVn\Sdk\Client\NhanhVnClient;

// Mới
use Puleeno\NhanhVn\Client\NhanhVnClient;
```

### 📦 Installation & Update

#### 1. Backup Project
```bash
# Backup toàn bộ project
cp -r your-project your-project-backup
```

#### 2. Update Dependencies
```bash
# Update SDK
composer update puleeno/nhanh-vn-sdk

# Hoặc cài mới
composer require puleeno/nhanh-vn-sdk:^0.4.0
```

#### 3. Update Code

##### Namespace Updates
```bash
# Tìm và thay thế tất cả namespace cũ
find . -name "*.php" -exec sed -i 's/NhanhVn\\Sdk/Puleeno\\NhanhVn/g' {} \;
```

##### Method Signature Updates
```php
// Cũ - OrderModule
$orders = $client->orders()->search($criteria);

// Mới - OrderModule (không thay đổi)
$orders = $client->orders()->search($criteria);

// Cũ - Shipping (không có)
// Mới - Shipping Module
$cities = $client->shipping()->searchCities();
$districts = $client->shipping()->searchDistricts(1);
```

### 🧪 Testing After Upgrade

#### 1. Test Basic Functionality
```php
// Test Product Module
$products = $client->products()->search(['page' => 1, 'perPage' => 5]);
echo "Products found: " . count($products) . "\n";

// Test Customer Module
$customers = $client->customers()->search(['page' => 1, 'perPage' => 5]);
echo "Customers found: " . count($customers) . "\n";

// Test Order Module (NEW)
$orders = $client->orders()->search(['page' => 1, 'perPage' => 5]);
echo "Orders found: " . count($orders) . "\n";

// Test Shipping Module (NEW)
$cities = $client->shipping()->searchCities();
echo "Cities found: " . count($cities) . "\n";
```

#### 2. Test Error Handling
```php
try {
    $result = $client->products()->search(['invalid' => 'data']);
} catch (Exception $e) {
    echo "Error handling works: " . $e->getMessage() . "\n";
}
```

#### 3. Test Cache System
```php
// Test cache status
$cacheStatus = $client->products()->getCacheStatus();
echo "Cache status: " . json_encode($cacheStatus) . "\n";

// Test cache clearing
$client->products()->clearCache();
echo "Cache cleared successfully\n";
```

### 🔧 Troubleshooting

#### Common Issues

##### 1. PHP Version Error
```
Fatal error: Uncaught Error: Call to undefined function array_is_list()
```
**Giải pháp:** Upgrade PHP lên 8.1+

##### 2. Namespace Not Found
```
Fatal error: Class "NhanhVn\Sdk\Client\NhanhVnClient" not found
```
**Giải pháp:** Update namespace thành `Puleeno\NhanhVn\Client\NhanhVnClient`

##### 3. Method Not Found
```
Fatal error: Call to undefined method prepareSearchCriteria()
```
**Giải pháp:** Method này đã được thêm vào OrderManager trong v0.4.0

##### 4. Memory Issues
```
Fatal error: Allowed memory size exhausted
```
**Giải pháp:** SDK v0.4.0 có memory management tự động, tăng PHP memory limit nếu cần

### 📚 Migration Examples

#### Before (v0.3.x)
```php
<?php
use NhanhVn\Sdk\Client\NhanhVnClient;

$client = NhanhVnClient::getInstance();
$products = $client->products()->search(['keyword' => 'iPhone']);
```

#### After (v0.4.0)
```php
<?php
use Puleeno\NhanhVn\Client\NhanhVnClient;

$client = NhanhVnClient::getInstance();

// Product Module (không thay đổi)
$products = $client->products()->search(['keyword' => 'iPhone']);

// Order Module (NEW)
$orders = $client->orders()->search(['page' => 1, 'perPage' => 10]);

// Shipping Module (NEW)
$cities = $client->shipping()->searchCities();
```

### 🚀 New Features to Explore

#### 1. Order Management
```php
// Search orders by status
$newOrders = $client->orders()->getByStatuses(['New']);

// Search orders by date range
$recentOrders = $client->orders()->getByDateRange(
    '2024-12-01 00:00:00',
    '2024-12-31 23:59:59'
);

// Update order status
$client->orders()->updateStatus($orderId, 'Confirmed');

// Send to carrier
$client->orders()->sendToCarrier($orderId);
```

#### 2. Shipping Locations
```php
// Get all cities
$cities = $client->shipping()->searchCities();

// Get districts of a city
$districts = $client->shipping()->searchDistricts(1); // Hanoi

// Get wards of a district
$wards = $client->shipping()->searchWards(101); // Hoan Kiem

// Search by name
$hanoiResults = $client->shipping()->searchByName('Hà', 'CITY');
```

#### 3. Enhanced Caching
```php
// Check cache status
$cacheStatus = $client->products()->getCacheStatus();

// Clear specific cache
$client->products()->clearCache();

// Check if cache is available
$isAvailable = $client->products()->isCacheAvailable();
```

### 📞 Support

Nếu gặp vấn đề trong quá trình upgrade:

- **Documentation**: [GitHub Repository](https://github.com/puleeno/nhanh-vn-sdk)
- **Issues**: [GitHub Issues](https://github.com/puleeno/nhanh-vn-sdk/issues)
- **Email**: puleeno@gmail.com
- **Hotline**: 0981272899

### 🔮 Next Steps

Sau khi upgrade thành công lên v0.4.0:

1. **Test tất cả functionality** cũ và mới
2. **Update documentation** và examples
3. **Train team** về các tính năng mới
4. **Monitor performance** và cache usage
5. **Plan upgrade** lên v0.5.0 (Q1 2025)

---

**Happy Upgrading! 🚀**

*Nhanh.vn PHP SDK v0.4.0 - Giải pháp tích hợp hoàn chỉnh cho Nhanh.vn API*
