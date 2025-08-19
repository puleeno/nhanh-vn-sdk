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
