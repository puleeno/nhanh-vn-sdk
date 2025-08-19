# Nhanh.vn SDK

A comprehensive PHP SDK for integrating with Nhanh.vn API, following SOLID principles and modern design patterns.

## Features

- **OAuth 2.0 Integration** - Secure authentication with Nhanh.vn
- **Product Management API** - Complete product CRUD operations
- **Product External Images API** - Manage product images from external CDNs
- **Batch Image Operations** - Handle multiple product images efficiently
- **Customer Management API** - Search and manage customer data
- **Modular Architecture** - Clean separation of concerns
- **Comprehensive Validation** - Input validation and error handling
- **Logging Integration** - Built-in logging with Monolog support
- **Exception Handling** - Custom exceptions for different error types
- **DTO Pattern** - Data Transfer Objects for request/response handling

## API Documentation

### Product Management
- [Product API Documentation](api/product.md) - Complete product operations
- [Product External Images](api/product.md#product-external-images) - Image management

### Customer Management
- [Customer API Documentation](api/customer.md) - Customer search and management

### Authentication
- [OAuth Documentation](api/oauth.md) - Authentication flow and token management

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

See the [examples](examples/) directory for complete usage examples:

- [Product Management](examples/public/)
- [Customer Search](examples/public/search_customers.php)
- [Authentication](examples/public/)

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
