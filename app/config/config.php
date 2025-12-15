<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'attendance_app');
define('DB_USER', 'root');
define('DB_PASS', '');

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
$path     = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';

define('BASE_URL', $protocol . $host . $path);
