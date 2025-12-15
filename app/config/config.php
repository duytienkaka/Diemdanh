<?php
// app/config/config.php

define('DB_HOST', 'localhost');
define('DB_NAME', 'attendance_app');
define('DB_USER', 'root');
define('DB_PASS', ''); // khi lên InfinityFree sẽ sửa thành user + password host cung cấp

// Tự động nhận BASE_URL dựa trên domain hiện tại
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
$path     = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';

define('BASE_URL', $protocol . $host . $path);
