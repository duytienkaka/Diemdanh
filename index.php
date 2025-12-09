<?php
// index.php
session_start();

require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/core/Controller.php';

// Autoload đơn giản
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/app/controllers/' . $class . '.php',
        __DIR__ . '/app/controllers/Admin/' . $class . '.php',
        __DIR__ . '/app/controllers/Monitor/' . $class . '.php',
        __DIR__ . '/app/controllers/Student/' . $class . '.php',
        __DIR__ . '/app/models/' . $class . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

ClassSession::autoUpdateStatuses();

// Lấy controller & action
$controllerName = $_GET['controller'] ?? 'auth';
$actionName     = $_GET['action'] ?? 'login';

function resolveControllerClass(string $name): string
{
    $name = strtolower($name);
    switch ($name) {
        case 'auth':
            return 'AuthController';

        case 'admin_dashboard':
            return 'AdminDashboardController';

        case 'monitor_dashboard':
            return 'MonitorDashboardController';
        case 'monitor_attendance':
            return 'AttendanceController';

        case 'student_dashboard':
            return 'StudentDashboardController';

        case 'admin_class_session':
            return 'AdminClassSessionController';

        case 'admin_student':
            return 'AdminStudentController';
        case 'admin_subject':
            return 'AdminSubjectController';
        case 'admin_report':
            return 'AdminReportController';
        case 'admin_semester':
            return 'AdminSemesterController';

        default:
            return 'AuthController';
    }
}



$controllerClass = resolveControllerClass($controllerName);

if (!class_exists($controllerClass)) {
    die("Controller not found: $controllerClass");
}

$controller = new $controllerClass();

if (!method_exists($controller, $actionName)) {
    die("Action not found: $actionName");
}

$controller->$actionName();
