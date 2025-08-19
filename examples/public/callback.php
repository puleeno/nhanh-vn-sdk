<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OAuth Callback - Nhanh.vn SDK</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php
/**
 * Nhanh.vn SDK v2.0 - OAuth Callback Handler
 *
 * Xử lý callback từ Nhanh.vn OAuth và đổi access code lấy access token
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Examples\OAuthExample;

// Khởi tạo ứng dụng
$app = new OAuthExample();

// Xử lý callback
$app->handleCallback();
?>
</body>
</html>
