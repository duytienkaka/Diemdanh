<?php
// app/core/Controller.php

class Controller
{
    protected function view(string $viewPath, array $data = [], string $layout = 'main')
    {
        extract($data);

        $viewFile   = __DIR__ . '/../views/' . $viewPath . '.php';
        $layoutFile = __DIR__ . '/../views/layouts/' . $layout . '.php';

        if (!file_exists($viewFile)) {
            die("View not found: $viewPath");
        }

        if (file_exists($layoutFile)) {
            include $layoutFile; // layout sẽ include $viewFile
        } else {
            include $viewFile;
        }
    }

    protected function redirect(string $path)
    {
        header('Location: ' . BASE_URL . $path);
        exit;
    }

    protected function requireLogin()
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('index.php?controller=auth&action=login');
        }
    }

    protected function requireRole(array $roles)
    {
        $this->requireLogin();

        $role = $_SESSION['role'] ?? null;
        if (!in_array($role, $roles, true)) {
            die('Bạn không có quyền truy cập chức năng này.');
        }
    }
}
