# Nhanh.vn SDK v0.4.0

A comprehensive PHP SDK for integrating with Nhanh.vn API, following SOLID principles and modern design patterns. This version introduces complete Order Management and new Shipping Location APIs.

## 📸 Examples & Demo

![Nhanh.vn SDK Examples](images/examples.png)

**Comprehensive Examples Collection** - The SDK provides a complete set of working examples demonstrating all major features and API integrations. Each example is designed to be production-ready and follows best practices for error handling, validation, and performance optimization.

### 🎯 What You'll Find in Examples

- **🔐 Authentication Examples** - OAuth flow, token management, and secure API access
- **📦 Product Management** - CRUD operations, category management, image handling, and batch operations
- **👥 Customer Operations** - Search, add, update, and batch customer management
- **📋 Order Processing** - Complete order lifecycle from creation to fulfillment
- **🚚 Shipping & Logistics** - Location services, carrier management, and shipping calculations
- **⚡ Advanced Features** - Client builder patterns, caching strategies, and performance optimization

### 🚀 Getting Started with Examples

All examples are located in the `examples/public/` directory and can be run directly in your browser. Each example includes:

- **Complete working code** with proper error handling
- **Vietnamese language support** for user interfaces
- **Responsive design** that works on all devices
- **Detailed comments** explaining each step
- **Best practices** for production deployment

### 📱 Interactive Demo Interface

The examples include a modern, responsive web interface that showcases:

- **Grid-based navigation** for easy access to all examples
- **Category organization** by functionality (Products, Orders, Customers, Shipping)
- **Visual feedback** with hover effects and animations
- **Mobile-friendly design** for testing on any device
- **Direct links** to relevant documentation and source code

### 🔧 Technical Highlights

Examples demonstrate advanced SDK features:

- **Memory Management** - Automatic cleanup and optimization
- **Caching Strategies** - Smart cache implementation for location data
- **Error Handling** - Comprehensive exception management
- **Validation** - Input validation with Vietnamese error messages
- **Logging** - Integration with Monolog for debugging
- **Performance** - Optimized API calls and response handling

## 🚀 Supported Nhanh.vn API Versions

### ✅ Currently Supported
- **Nhanh.vn API v2.0** - Full support with all implemented modules

### 🔄 Coming Soon
- **Nhanh.vn API v3.0** - Beta version, planned for SDK v1.0.0
  - Enhanced performance and new features
  - Backward compatibility with v2.0
  - New modules: Analytics, Advanced Reporting, Webhooks

## 📋 API Implementation Status (v2.0)

### ✅ Core Modules (100% Complete)
- **Products Module** - Complete CRUD operations, categories, brands, images
- **Orders Module** - Complete order management, search, update, status management
- **Customers Module** - Complete customer search, add, batch operations
- **Shipping Module** - Complete location system (City, District, Ward), carriers

### 🔄 Next Priority Modules
- **Bill Module** - Inventory management, stock operations
- **Store Module** - Warehouse, staff, branch management
- **Statistics Module** - Reports and analytics

## Features

- **OAuth 2.0 Integration** - Secure authentication with Nhanh.vn
- **Product Management API** - Complete product CRUD operations
- **Product External Images API** - Manage product images from external CDNs
- **Batch Image Operations** - Handle multiple product images efficiently
- **Customer Management API** - Search and manage customer data
- **Order Management API** - Complete order CRUD operations and status management
- **Shipping Location API** - 3-tier location system (City, District, Ward)
- **Modular Architecture** - Clean separation of concerns
- **Comprehensive Validation** - Input validation and error handling with Vietnamese messages
- **Logging Integration** - Built-in logging with Monolog support
- **Exception Handling** - Custom exceptions for different error types
- **DTO Pattern** - Data Transfer Objects for request/response handling
- **Smart Caching** - Intelligent cache system with TTL optimization
- **Memory Management** - Automatic cleanup and optimization

## 🚀 Quick Navigation

- **[Examples Dashboard](examples/public/index.php)** - Interactive examples interface
- **[API Reference](v2/README.md)** - Complete API documentation
- **[Quick Start](#quick-start)** - Get up and running in minutes
- **[Architecture](#architecture)** - Understanding the SDK structure

## API Documentation
- [Product API Documentation](v2/product/README.md) - Complete product operations
- [Product External Images](v2/product/README.md#thêm-ảnh-sản-phẩm) - Image management

### 👥 Customer Management
- [Customer API Documentation](v2/customer/README.md) - Customer search and management
- [Customer API Details](v2/customer/customer.md) - Detailed API reference

### 📋 Order Management
- [Order API Documentation](v2/order/README.md) - Complete order operations
- [Order Update API](v2/order/order-update.md) - Order update implementation
- [Order API Details](v2/order/order.md) - Detailed API reference

### 🚚 Shipping & Location
- [Shipping API Documentation](v2/shipping/shipping.md) - Location management and carrier information

### 🔐 Authentication & Architecture
- [OAuth Documentation](v2/api/oauth.md) - Authentication flow and token management
- [Flow Diagram](v2/flow-diagram.md) - System architecture and data flow

## Quick Start

### Installation

```bash
composer require puleeno/nhanh-vn-sdk
```

### Basic Usage

```php
use Puleeno\NhanhVn\Client\NhanhVnClient;

// Initialize client
$client = NhanhVnClient::getInstance();

// Use product module
$products = $client->product()->getAll();

// Use customer module
$customers = $client->customer()->search(['type' => 1]);
```

## Architecture

The SDK follows a layered architecture pattern:

- **Client Layer** - Main entry point and configuration
- **Module Layer** - High-level business operations
- **Manager Layer** - Orchestration of business logic
- **Service Layer** - Core business logic implementation
- **Repository Layer** - Data access and entity creation
- **Entity Layer** - Data models and validation

## Examples

### 🎯 Interactive Examples Interface

Experience the SDK capabilities through our comprehensive examples collection:

- **[Examples Dashboard](examples/public/index.php)** - Modern grid-based interface showcasing all features
- **[Live Demo](examples/public/)** - Run examples directly in your browser

### 📚 Individual Examples

#### 🔐 Authentication & Setup
- [OAuth Flow](examples/public/oauth.php) - Complete OAuth authentication process
- [Test Boot File](examples/public/test_boot.php) - Client initialization testing
- [OAuth Callback](examples/public/callback.php) - OAuth callback handling

#### 📦 Product Management
- [Product CRUD](examples/public/get_products.php) - List, search, and manage products
- [Product with Logger](examples/public/get_products_with_logger.php) - Advanced logging integration
- [Categories](examples/public/get_categories.php) - Product category management
- [Product Details](examples/public/get_product_detail.php) - Detailed product information
- [Add Products](examples/public/add_product.php) - Create new products
- [Product Images](examples/public/add_product_images.php) - External image management

#### 👥 Customer Operations
- [Customer Search](examples/public/search_customers.php) - Advanced customer search and filtering
- [Add Customers](examples/public/add_customer.php) - Create and batch customer operations

#### 📋 Order Management
- [Order Search](examples/public/get_orders.php) - Search, filter, and paginate orders
- [Add Orders](examples/public/add_order.php) - Create new orders with shipping options
- [Update Orders](examples/public/update_order.php) - Status updates, payment, and shipping

#### 🚚 Shipping & Location
- [Location Services](examples/public/get_locations.php) - City, District, Ward management
- [Shipping Carriers](examples/public/get_shipping_carriers.php) - Carrier and service information
- [Shipping Calculator](examples/public/calculate_shipping_fee.php) - Shipping cost calculations

#### ⚡ Advanced Features
- [Client Builder](examples/public/client_builder_demo.php) - Modern client creation patterns
- [Legacy Orders](examples/public/orders.php) - Legacy order API examples

### 🚀 Getting Started

1. **Clone the repository** and navigate to examples directory
2. **Configure your credentials** in `examples/boot/client.php`
3. **Start with OAuth** - Run `examples/public/oauth.php` first
4. **Explore features** - Use the examples dashboard for easy navigation
5. **Customize code** - Each example is production-ready and well-commented

## Version Compatibility

| SDK Version | Nhanh.vn API | PHP Version | Status |
|-------------|---------------|-------------|---------|
| v0.4.0 | v2.0 | 8.1+ | ✅ Stable |
| v0.3.x | v2.0 | 8.0+ | ⚠️ Deprecated |
| v0.2.x | v1.0 | 7.4+ | ❌ EOL |
| v0.1.x | v1.0 | 7.4+ | ❌ EOL |

## Migration Path

### From v0.3.x to v0.4.0
- PHP 8.1+ required
- Namespace changes: `NhanhVn\Sdk` → `Puleeno\NhanhVn`
- New Order Update and Shipping Location APIs
- Enhanced validation and error handling

### Future: v0.4.0 to v1.0.0
- Nhanh.vn API v3.0 support
- Enhanced performance and new features
- Backward compatibility maintained
- New analytics and reporting modules

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
