<?php
// app/config/config.php

define('DB_HOST', 'sql104.infinityfree.com');
define('DB_NAME', 'if0_40578862_qldiemdanh');
define('DB_USER', 'if0_40578862');
define('DB_PASS', 'ThaoVan1008');

// Tự động nhận BASE_URL dựa trên domain hiện tại
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
$path     = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';

define('BASE_URL', $protocol . $host . $path);
