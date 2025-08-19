<?php
/**
 * Example: Tìm kiếm khách hàng từ Nhanh.vn API sử dụng SDK
 */

require_once __DIR__ . '/../boot/client.php';

// HTML header
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>👥 Tìm kiếm khách hàng từ Nhanh.vn API</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>👥 Tìm kiếm khách hàng từ Nhanh.vn API sử dụng SDK</h1>
        <hr>

        <div class="section">
            <h2>📋 Thông tin Debug</h2>
            <div class="debug-info">
                <p><strong>Script:</strong> <?php echo htmlspecialchars(__FILE__); ?></p>
                <p><strong>Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
                <p><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></p>
            </div>
        </div>

<?php

try {
    // Kiểm tra xem client có sẵn sàng không
    if (!isClientReady()) {
        echo '<div class="status error">';
        echo '<h3>❌ Chưa có access token</h3>';
        echo '<p>Hãy chạy OAuth flow trước!</p>';
        echo '<p><a href="index.php" class="btn btn-primary">🔐 Chạy OAuth Flow</a></p>';
        echo '</div>';
        echo '</div></body></html>';
        exit(1);
    }

    // Hiển thị thông tin client
    $clientInfo = getClientInfo();
    echo '<div class="status success">';
    echo '<h3>✅ Đã có access token</h3>';
    echo '<p><strong>Token:</strong> ' . htmlspecialchars($clientInfo['accessTokenPreview']) . '</p>';
    echo '</div>';

    // Khởi tạo SDK client
    echo '<div class="section">';
    echo '<h3>🚀 Khởi tạo SDK Client</h3>';

    try {
        // Sử dụng boot file để khởi tạo client
        $client = bootNhanhVnClientSilent();

        echo '<div class="status success">';
        echo '<h4>✅ SDK client đã sẵn sàng!</h4>';
        echo '<p><strong>Logger:</strong> NullLogger (không log)</p>';
        echo '</div>';

    } catch (Exception $e) {
        echo '<div class="status error">';
        echo '<h4>❌ Lỗi khởi tạo SDK</h4>';
        echo '<p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<p><strong>Stack trace:</strong></p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        echo '</div>';
        echo '</div></body></html>';
        exit(1);
    }
    echo '</div>';

    // Khởi tạo Customer module
    echo '<div class="section">';
    echo '<h3>👥 Khởi tạo Customer Module</h3>';

    try {
        // Sử dụng Customer module từ singleton client
        $customerModule = $client->customers();

        echo '<div class="status success">';
        echo '<h4>✅ Customer module đã sẵn sàng!</h4>';
        echo '<p><strong>Module:</strong> ' . get_class($customerModule) . '</p>';
        echo '<p><strong>Client Instance:</strong> ' . get_class($client) . '</p>';
        echo '</div>';

    } catch (Exception $e) {
        echo '<div class="status error">';
        echo '<h4>❌ Lỗi khởi tạo Customer Module</h4>';
        echo '<p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<p><strong>Stack trace:</strong></p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        echo '</div>';
        echo '</div></body></html>';
        exit(1);
    }
    echo '</div>';

    // Bắt đầu các example
    echo '<div class="section">';
    echo '<h3>🔍 Customer Search Examples</h3>';

    // Example 1: Search all customers with pagination
    echo '<div class="example info">';
    echo '<h4>Example 1: Search All Customers (Page 1, 10 per page)</h4>';

    try {
        $response = $customerModule->getAll(1, 10);

        if ($response->isSuccess()) {
            echo '<div class="success">✅ Search successful!</div>';
            echo '<p><strong>Total Customers:</strong> ' . $response->getTotalCustomers() . '</p>';
            echo '<p><strong>Total Pages:</strong> ' . $response->getTotalPages() . '</p>';

            $customers = $response->getCustomers();
            if (!empty($customers)) {
                echo '<h5>Customers Found:</h5>';
                foreach ($customers as $index => $customer) {
                    echo '<div class="customer-item">';
                    echo '<strong>Customer ' . ($index + 1) . ':</strong><br>';
                    echo 'ID: ' . $customer['id'] . '<br>';
                    echo 'Name: ' . $customer['name'] . '<br>';
                    echo 'Mobile: ' . $customer['mobile'] . '<br>';
                    echo 'Type: ' . $customer['type'] . '<br>';
                    echo 'Email: ' . $customer['email'] . '<br>';
                    echo '</div>';
                }
            }

            // Show summary
            $summary = $response->getSummary();
            echo '<h5>Summary Statistics:</h5>';
            echo '<pre>' . json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';

        } else {
            echo '<div class="error">❌ Search failed!</div>';
            echo '<p><strong>Error Messages:</strong></p>';
            echo '<pre>' . $response->getAllMessagesAsString() . '</pre>';
        }

    } catch (Exception $e) {
        echo '<div class="error">❌ Exception occurred: ' . $e->getMessage() . '</div>';
    }
    echo '</div>';

    // Example 2: Search customer by ID
    echo '<div class="example info">';
    echo '<h4>Example 2: Search Customer by ID (ID: 1)</h4>';

    try {
        $response = $customerModule->searchById(1);

        if ($response->isSuccess()) {
            echo '<div class="success">✅ Search successful!</div>';
            echo '<p><strong>Total Customers Found:</strong> ' . $response->getTotalCustomers() . '</p>';

            $customers = $response->getCustomers();
            if (!empty($customers)) {
                $customer = $customers[0];
                echo '<div class="customer-item">';
                echo '<strong>Customer Details:</strong><br>';
                echo 'ID: ' . $customer['id'] . '<br>';
                echo 'Name: ' . $customer['name'] . '<br>';
                echo 'Mobile: ' . $customer['mobile'] . '<br>';
                echo 'Type: ' . $customer['type'] . '<br>';
                echo 'Email: ' . $customer['email'] . '<br>';
                echo 'Address: ' . $customer['address'] . '<br>';
                echo 'Total Money: ' . number_format($customer['totalMoney']) . ' VND<br>';
                echo 'Points: ' . $customer['points'] . '<br>';
                echo '</div>';
            }
        } else {
            echo '<div class="error">❌ Search failed!</div>';
            echo '<p><strong>Error Messages:</strong></p>';
            echo '<pre>' . $response->getAllMessagesAsString() . '</pre>';
        }

    } catch (Exception $e) {
        echo '<div class="error">❌ Exception occurred: ' . $e->getMessage() . '</div>';
    }
    echo '</div>';

    // Example 3: Search customer by mobile
    echo '<div class="example info">';
    echo '<h4>Example 3: Search Customer by Mobile (0981234567)</h4>';

    try {
        $response = $customerModule->searchByMobile('0981234567');

        if ($response->isSuccess()) {
            echo '<div class="success">✅ Search successful!</div>';
            echo '<p><strong>Total Customers Found:</strong> ' . $response->getTotalCustomers() . '</p>';

            $customers = $response->getCustomers();
            if (!empty($customers)) {
                foreach ($customers as $customer) {
                    echo '<div class="customer-item">';
                    echo '<strong>Customer Found:</strong><br>';
                    echo 'ID: ' . $customer['id'] . '<br>';
                    echo 'Name: ' . $customer['name'] . '<br>';
                    echo 'Mobile: ' . $customer['mobile'] . '<br>';
                    echo 'Type: ' . $customer['type'] . '<br>';
                    echo '</div>';
                }
            } else {
                echo '<p>No customers found with this mobile number.</p>';
            }
        } else {
            echo '<div class="error">❌ Search failed!</div>';
            echo '<p><strong>Error Messages:</strong></p>';
            echo '<pre>' . $response->getAllMessagesAsString() . '</pre>';
        }

    } catch (Exception $e) {
        echo '<div class="error">❌ Exception occurred: ' . $e->getMessage() . '</div>';
    }
    echo '</div>';

    // Example 4: Get customers by type
    echo '<div class="example info">';
    echo '<h4>Example 4: Get Retail Customers (Type: 1)</h4>';

    try {
        $response = $customerModule->getRetailCustomers(1, 5);

        if ($response->isSuccess()) {
            echo '<div class="success">✅ Search successful!</div>';
            echo '<p><strong>Total Retail Customers:</strong> ' . $response->getTotalCustomers() . '</p>';
            echo '<p><strong>Page:</strong> 1, <strong>Per Page:</strong> 5</p>';

            $customers = $response->getCustomers();
            if (!empty($customers)) {
                echo '<h5>Retail Customers:</h5>';
                foreach ($customers as $index => $customer) {
                    echo '<div class="customer-item">';
                    echo '<strong>Customer ' . ($index + 1) . ':</strong><br>';
                    echo 'ID: ' . $customer['id'] . '<br>';
                    echo 'Name: ' . $customer['name'] . '<br>';
                    echo 'Type: ' . $customer['type'] . ' (Retail)<br>';
                    echo 'Mobile: ' . $customer['mobile'] . '<br>';
                    echo '</div>';
                }
            }
        } else {
            echo '<div class="error">❌ Search failed!</div>';
            echo '<p><strong>Error Messages:</strong></p>';
            echo '<pre>' . $response->getAllMessagesAsString() . '</pre>';
        }

    } catch (Exception $e) {
        echo '<div class="error">❌ Exception occurred: ' . $e->getMessage() . '</div>';
    }
    echo '</div>';

    // Example 5: Get customers by date range
    echo '<div class="example info">';
    echo '<h4>Example 5: Get Customers Updated in Date Range</h4>';

    try {
        $fromDate = '2024-01-01 00:00:00';
        $toDate = '2024-12-31 23:59:59';

        $response = $customerModule->getByDateRange($fromDate, $toDate, 1, 3);

        if ($response->isSuccess()) {
            echo '<div class="success">✅ Search successful!</div>';
            echo '<p><strong>Date Range:</strong> ' . $fromDate . ' to ' . $toDate . '</p>';
            echo '<p><strong>Total Customers Found:</strong> ' . $response->getTotalCustomers() . '</p>';

            $customers = $response->getCustomers();
            if (!empty($customers)) {
                echo '<h5>Customers Updated in Range:</h5>';
                foreach ($customers as $index => $customer) {
                    echo '<div class="customer-item">';
                    echo '<strong>Customer ' . ($index + 1) . ':</strong><br>';
                    echo 'ID: ' . $customer['id'] . '<br>';
                    echo 'Name: ' . $customer['name'] . '<br>';
                    echo 'Mobile: ' . $customer['mobile'] . '<br>';
                    echo 'Type: ' . $customer['type'] . '<br>';
                    echo '</div>';
                }
            }
        } else {
            echo '<div class="error">❌ Search failed!</div>';
            echo '<p><strong>Error Messages:</strong></p>';
            echo '<pre>' . $response->getAllMessagesAsString() . '</pre>';
        }

    } catch (Exception $e) {
        echo '<div class="error">❌ Exception occurred: ' . $e->getMessage() . '</div>';
    }
    echo '</div>';

    // Example 6: Validation examples
    echo '<div class="example info">';
    echo '<h4>Example 6: Request Validation Examples</h4>';

    // Valid request
    $validRequest = [
        'page' => 1,
        'icpp' => 20,
        'type' => 1
    ];

    echo '<h5>Valid Request:</h5>';
    echo '<pre>' . json_encode($validRequest, JSON_PRETTY_PRINT) . '</pre>';

    if ($customerModule->validateSearchRequest($validRequest)) {
        echo '<div class="success">✅ Request is valid</div>';
    } else {
        echo '<div class="error">❌ Request is invalid</div>';
        $errors = $customerModule->getSearchRequestErrors($validRequest);
        echo '<p><strong>Errors:</strong></p>';
        echo '<pre>' . json_encode($errors, JSON_PRETTY_PRINT) . '</pre>';
    }

    // Invalid request
    $invalidRequest = [
        'page' => 0, // Invalid: page must be > 0
        'icpp' => 100, // Invalid: icpp must be <= 50
        'mobile' => '123' // Invalid: mobile format
    ];

    echo '<h5>Invalid Request:</h5>';
    echo '<pre>' . json_encode($invalidRequest, JSON_PRETTY_PRINT) . '</pre>';

    if ($customerModule->validateSearchRequest($invalidRequest)) {
        echo '<div class="success">✅ Request is valid</div>';
    } else {
        echo '<div class="error">❌ Request is invalid</div>';
        $errors = $customerModule->getSearchRequestErrors($invalidRequest);
        echo '<p><strong>Errors:</strong></p>';
        echo '<pre>' . json_encode($errors, JSON_PRETTY_PRINT) . '</pre>';
    }

    echo '</div>';

    echo '<div class="example info">';
    echo '<h4>Summary</h4>';
    echo '<p>This example demonstrates the Customer module functionality including:</p>';
    echo '<ul>';
    echo '<li>Searching all customers with pagination</li>';
    echo '<li>Searching customers by ID</li>';
    echo '<li>Searching customers by mobile number</li>';
    echo '<li>Filtering customers by type (retail, wholesale, agent)</li>';
    echo '<li>Filtering customers by date range</li>';
    echo '<li>Request validation and error handling</li>';
    echo '</ul>';
    echo '<p>The module follows SOLID principles and provides a clean, maintainable API for customer operations.</p>';
    echo '</div>';

    echo '</div>'; // End of examples section

} catch (Exception $e) {
    echo '<div class="status error">';
    echo '<h3>❌ Lỗi chung</h3>';
    echo '<p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p><strong>Stack trace:</strong></p>';
    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '</div>';
}

echo '</div>'; // End of container
echo '</body>';
echo '</html>';
?>
