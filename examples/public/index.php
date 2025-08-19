<?php
/**
 * Nhanh.vn SDK v2.0 - OAuth Example
 *
 * Khởi tạo ứng dụng và hiển thị thông tin server
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Examples\OAuthExample;

// Khởi tạo ứng dụng
$app = new OAuthExample();

// Hiển thị thông tin server
$app->showServerInfo();

// Hiển thị link OAuth
$app->showOAuthLink();

// Hiển thị trạng thái hiện tại
$app->showCurrentStatus();
