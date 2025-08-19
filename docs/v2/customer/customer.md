# Customer API Documentation

## Overview

The Customer module provides comprehensive functionality for searching and managing customer data in the Nhanh.vn system. It supports various search criteria including ID, mobile number, customer type, and date ranges.

## API Endpoints

### Customer Search

**Endpoint**: `/api/customer/search`

**Description**: Search for customers using various criteria or retrieve a paginated list of all customers.

**Features**:
- Search by customer ID
- Search by mobile number
- Filter by customer type (retail, wholesale, agent)
- Filter by date ranges (last bought date, updated date)
- Pagination support (max 50 customers per page)
- Optimized for incremental updates using date filters

## Request Parameters

| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `page` | int | No | Page number (default: 1) | `1` |
| `icpp` | int | No | Items per page (max: 50, default: 10) | `20` |
| `id` | int | No | Search by customer ID | `12345` |
| `mobile` | string | No | Search by mobile number | `"0988999999"` |
| `lastBoughtDateFrom` | string | No | From date of last purchase (Y-m-d) | `"2022-09-25"` |
| `lastBoughtDateTo` | string | No | To date of last purchase (Y-m-d) | `"2022-09-26"` |
| `updatedAtFrom` | string | No | From update date (Y-m-d H:i:s) | `"2022-05-25 00:00:00"` |
| `updatedAtTo` | string | No | To update date (Y-m-d H:i:s) | `"2022-05-30 23:59:00"` |
| `type` | int | No | Customer type filter | `1` (retail), `2` (wholesale), `3` (agent) |

### Customer Types

| Type | Value | Description |
|------|-------|-------------|
| Retail | `1` | Khách lẻ |
| Wholesale | `2` | Khách buôn |
| Agent | `3` | Đại lý |

### Date Formats

- **Date only**: `Y-m-d` (e.g., `2022-09-25`)
- **Date with time**: `Y-m-d H:i:s` (e.g., `2022-05-25 00:00:00`)

## Response Structure

### Success Response

```json
{
    "code": 1,
    "data": {
        "totalPages": 5,
        "customers": [
            {
                "id": 12345,
                "type": 1,
                "name": "Nguyễn Văn A",
                "mobile": "0988999999",
                "email": "nguyenvana@example.com",
                "gender": 1,
                "address": "123 Đường ABC, Quận 1, TP.HCM",
                "birthday": "1990-01-01",
                "code": "KH001234",
                "level": "Thành viên",
                "group": "Nhóm A",
                "totalMoney": 5000000,
                "points": 500,
                "cityLocationId": 1,
                "districtLocationId": 1,
                "wardLocationId": 1,
                "saleName": "Nhân viên A",
                "startedDate": "2020-01-01",
                "taxCode": "",
                "businessName": "",
                "businessAddress": ""
            }
        ]
    }
}
```

### Error Response

```json
{
    "code": 0,
    "messages": [
        "Invalid mobile number format",
        "Date range is invalid"
    ]
}
```

### Customer Fields

| Field | Type | Description |
|-------|------|-------------|
| `id` | int | Customer ID |
| `type` | int | Customer type (1=retail, 2=wholesale, 3=agent) |
| `name` | string | Customer name |
| `mobile` | string | Mobile phone number |
| `email` | string | Email address |
| `gender` | int | Gender (1=male, 2=female, null=unknown) |
| `address` | string | Customer address |
| `birthday` | string | Birthday (Y-m-d format) |
| `code` | string | Customer code |
| `level` | string | Customer level |
| `group` | string | Customer group |
| `totalMoney` | double | Total money spent |
| `points` | int | Loyalty points |
| `cityLocationId` | int | City ID |
| `districtLocationId` | int | District ID |
| `wardLocationId` | int | Ward ID |
| `saleName` | string | Assigned sales person |
| `startedDate` | string | First purchase date |
| `taxCode` | string | Tax code |
| `businessName` | string | Business name |
| `businessAddress` | string | Business address |

## Usage Examples

### Basic Search

```php
use Puleeno\NhanhVn\Modules\CustomerModule;

// Search all customers with pagination
$response = $customerModule->getAll(1, 20);

if ($response->isSuccess()) {
    $customers = $response->getCustomers();
    $totalPages = $response->getTotalPages();

    foreach ($customers as $customer) {
        echo "Customer: " . $customer['name'] . "\n";
    }
}
```

### Search by ID

```php
// Search specific customer by ID
$response = $customerModule->searchById(12345);

if ($response->isSuccess() && $response->hasCustomers()) {
    $customer = $response->getFirstCustomer();
    echo "Found customer: " . $customer['name'] . "\n";
}
```

### Search by Mobile

```php
// Search customer by mobile number
$response = $customerModule->searchByMobile('0988999999');

if ($response->isSuccess()) {
    $customers = $response->getCustomers();
    if (!empty($customers)) {
        echo "Found " . count($customers) . " customer(s)\n";
    }
}
```

### Filter by Type

```php
// Get retail customers only
$response = $customerModule->getRetailCustomers(1, 10);

// Get wholesale customers
$response = $customerModule->getWholesaleCustomers(1, 15);

// Get agent customers
$response = $customerModule->getAgentCustomers(1, 20);
```

### Filter by Date Range

```php
// Get customers updated in specific date range
$fromDate = '2024-01-01 00:00:00';
$toDate = '2024-12-31 23:59:59';

$response = $customerModule->getByDateRange($fromDate, $toDate, 1, 25);
```

### Advanced Search

```php
// Complex search with multiple criteria
$searchParams = [
    'type' => 1, // Retail customers only
    'page' => 1,
    'icpp' => 25,
    'lastBoughtDateFrom' => '2024-01-01',
    'lastBoughtDateTo' => '2024-12-31'
];

$response = $customerModule->search($searchParams);
```

### Validation

```php
// Validate search parameters before making request
$searchParams = [
    'page' => 1,
    'icpp' => 100, // Invalid: exceeds max limit
    'mobile' => '123' // Invalid: wrong format
];

if ($customerModule->validateSearchRequest($searchParams)) {
    $response = $customerModule->search($searchParams);
} else {
    $errors = $customerModule->getSearchRequestErrors($searchParams);
    echo "Validation errors: " . json_encode($errors);
}
```

## Response Analysis

### Summary Statistics

```php
$summary = $response->getSummary();

echo "Total customers: " . $summary['totalCustomers'] . "\n";
echo "Total pages: " . $summary['totalPages'] . "\n";

if ($summary['hasData']) {
    echo "Type distribution:\n";
    echo "- Retail: " . $summary['typeDistribution']['retail'] . "\n";
    echo "- Wholesale: " . $summary['typeDistribution']['wholesale'] . "\n";
    echo "- Agent: " . $summary['typeDistribution']['agent'] . "\n";

    echo "Gender distribution:\n";
    echo "- Male: " . $summary['genderDistribution']['male'] . "\n";
    echo "- Female: " . $summary['genderDistribution']['female'] . "\n";

    echo "Field completeness:\n";
    echo "- With mobile: " . $summary['fieldCompleteness']['mobile'] . "\n";
    echo "- With email: " . $summary['fieldCompleteness']['email'] . "\n";
    echo "- With address: " . $summary['fieldCompleteness']['address'] . "\n";
}
```

### Filtering Results

```php
// Get customers by type
$retailCustomers = $response->getCustomersByType(1);
$wholesaleCustomers = $response->getCustomersByType(2);

// Get customers by gender
$maleCustomers = $response->getCustomersByGender(1);
$femaleCustomers = $response->getCustomersByGender(2);

// Get customers with specific fields
$customersWithMobile = $response->getCustomersWithMobile();
$customersWithEmail = $response->getCustomersWithEmail();
$customersWithAddress = $response->getCustomersWithAddress();
```

## Best Practices

### Pagination

- Use appropriate `icpp` values (10-50) based on your needs
- Implement proper pagination controls in your UI
- Consider caching results for better performance

### Date Filtering

- Use `updatedAtFrom` and `updatedAtTo` for incremental updates
- Store the last update timestamp to avoid re-fetching unchanged data
- Use appropriate date formats for each field type

### Search Optimization

- Use specific filters (ID, mobile) when possible instead of general search
- Combine multiple filters to narrow down results
- Validate parameters before making API calls

### Error Handling

```php
try {
    $response = $customerModule->search($searchParams);

    if ($response->isSuccess()) {
        // Process successful response
        $customers = $response->getCustomers();
    } else {
        // Handle API errors
        $errorMessages = $response->getAllMessagesAsString();
        echo "Search failed: " . $errorMessages;
    }
} catch (Exception $e) {
    // Handle exceptions
    echo "Exception occurred: " . $e->getMessage();
}
```

## Rate Limiting

- Maximum 50 customers per page
- Implement appropriate delays between requests
- Use date filtering to minimize data transfer

## Data Synchronization

For systems that need to keep customer data synchronized:

1. **Initial Sync**: Use general search without date filters
2. **Incremental Updates**: Use `updatedAtFrom` and `updatedAtTo` parameters
3. **Store Timestamps**: Keep track of last successful sync
4. **Error Handling**: Implement retry logic for failed requests

## Example Implementation

See [search_customers.php](../../examples/public/search_customers.php) for a complete working example that demonstrates all the features described in this documentation.
