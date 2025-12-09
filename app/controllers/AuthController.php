<?php
// app/controllers/AuthController.php

class AuthController extends Controller
{
    public function login()
    {
        // Nếu đã đăng nhập rồi thì chuyển thẳng
        if (!empty($_SESSION['user_id'])) {
            $this->redirectAfterLogin();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $errors = [];

            if ($username === '' || $password === '') {
                $errors[] = 'Vui lòng nhập đầy đủ tài khoản và mật khẩu.';
            } else {
                $user = User::findByUsername($username);
                if (!$user || $password != $user['password_hash']) {
                    $errors[] = 'Tài khoản hoặc mật khẩu không đúng.';
                }
            }

            if (empty($errors) && isset($user)) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role']    = $user['role'];

                $this->redirectAfterLogin();
            } else {
                $this->view('auth/login', [
                    'errors' => $errors,
                    'old'    => ['username' => $username],
                ], 'auth');
            }
        } else {
            $this->view('auth/login', [], 'auth');
        }
    }

    private function redirectAfterLogin()
    {
        $role = $_SESSION['role'] ?? 'student';

        if ($role === 'admin') {
            $this->redirect('index.php?controller=admin_dashboard&action=index');
        } elseif ($role === 'monitor') {
            $this->redirect('index.php?controller=monitor_dashboard&action=index');
        } else {
            $this->redirect('index.php?controller=student_dashboard&action=index');
        }
    }

    public function logout()
    {
        session_destroy();
        $this->redirect('index.php?controller=auth&action=login');
    }
}
