<?php
// app/controllers/AdminStudentController.php

class AdminStudentController extends Controller
{
    public function index()
    {
        $this->requireRole(['admin']);

        $students = Student::allWithUser();

        $this->view('admin/students/index', [
            'students' => $students,
        ], 'main');
    }

    public function create()
    {
        $this->requireRole(['admin']);

        $errors = [];
        $message = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username     = trim($_POST['username'] ?? '');
            $password     = trim($_POST['password'] ?? '');
            $studentCode  = trim($_POST['student_code'] ?? '');
            $fullName     = trim($_POST['full_name'] ?? '');
            $gender       = $_POST['gender'] ?? null;
            $phone        = trim($_POST['phone'] ?? '');
            $isMonitor    = isset($_POST['is_monitor']);

            if ($username === '' || $password === '' || $studentCode === '' || $fullName === '') {
                $errors[] = 'Vui lòng nhập đầy đủ Username, Mật khẩu, MSSV, Họ tên.';
            }

            if (empty($errors)) {
                try {
                    Student::createWithUser(
                        $username,
                        $password,
                        $studentCode,
                        $fullName,
                        $gender ?: null,
                        $phone ?: null,
                        $isMonitor
                    );
                    $message = 'Đã tạo sinh viên mới thành công.';
                    $this->redirect('index.php?controller=admin_student&action=index');
                } catch (Exception $e) {
                    $errors[] = 'Lỗi khi tạo sinh viên: ' . $e->getMessage();
                }
            }
        }

        $this->view('admin/students/create', [
            'errors'  => $errors,
            'message' => $message,
        ], 'main');
    }

    public function edit()
    {
        $this->requireRole(['admin']);

        $id = (int)($_GET['id'] ?? 0);
        $student = Student::findById($id);
        if (!$student) {
            die('Không tìm thấy sinh viên.');
        }

        $errors = [];
        $message = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $studentCode  = trim($_POST['student_code'] ?? '');
            $fullName     = trim($_POST['full_name'] ?? '');
            $gender       = $_POST['gender'] ?? null;
            $phone        = trim($_POST['phone'] ?? '');

            if ($studentCode === '' || $fullName === '') {
                $errors[] = 'Vui lòng nhập MSSV và Họ tên.';
            }

            if (empty($errors)) {
                try {
                    Student::updateWithUser(
                        $id,
                        $studentCode,
                        $fullName,
                        $gender ?: null,
                        $phone ?: null
                    );
                    $message = 'Đã cập nhật thông tin sinh viên.';
                    $student = Student::findById($id);
                    $this->redirect('index.php?controller=admin_student&action=index');
                } catch (Exception $e) {
                    $errors[] = 'Lỗi khi cập nhật: ' . $e->getMessage();
                }
            }
        }

        $this->view('admin/students/edit', [
            'student' => $student,
            'errors'  => $errors,
            'message' => $message,
        ], 'main');
    }

    public function delete()
    {
        $this->requireRole(['admin']);

        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            // Khóa sinh viên (is_active = 0)
            Student::setActive($id, false);
        }

        $this->redirect('index.php?controller=admin_student&action=index');
    }
    public function setActive()
    {
        $this->requireRole(['admin']);

        $id  = (int)($_GET['id'] ?? 0);
        $act = (int)($_GET['act'] ?? 1); // 1 = mở khóa, 0 = khóa

        if ($id > 0) {
            Student::setActive($id, $act === 1);
        }

        $this->redirect('index.php?controller=admin_student&action=index');
    }


    public function setMonitor()
    {
        $this->requireRole(['admin']);

        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            Student::setMonitor($id);
        }

        $this->redirect('index.php?controller=admin_student&action=index');
    }
}
