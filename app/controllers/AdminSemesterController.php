<?php
// app/controllers/AdminSemesterController.php

class AdminSemesterController extends Controller
{
    public function index()
    {
        $this->requireRole(['admin']);

        $semesters = Semester::all();

        $this->view('admin/semesters/index', [
            'semesters' => $semesters,
        ], 'main');
    }

    public function create()
    {
        $this->requireRole(['admin']);

        $errors = [];
        $message = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name       = trim($_POST['name'] ?? '');
            $startDate  = trim($_POST['start_date'] ?? '');
            $endDate    = trim($_POST['end_date'] ?? '');
            $isActive   = isset($_POST['is_active']);

            if ($name === '' || $startDate === '' || $endDate === '') {
                $errors[] = 'Vui lòng nhập tên học kỳ, ngày bắt đầu và ngày kết thúc.';
            }

            if (empty($errors)) {
                try {
                    Semester::create($name, $startDate, $endDate, $isActive);
                    $message = 'Đã tạo học kỳ mới.';
                    $this->redirect('index.php?controller=admin_semester&action=index');
                } catch (Exception $e) {
                    $errors[] = 'Lỗi khi tạo học kỳ: ' . $e->getMessage();
                }
            }
        }

        $this->view('admin/semesters/create', [
            'errors'  => $errors,
            'message' => $message,
        ], 'main');
    }

    public function edit()
    {
        $this->requireRole(['admin']);

        $id = (int)($_GET['id'] ?? 0);
        $semester = Semester::findById($id);
        if (!$semester) {
            die('Không tìm thấy học kỳ.');
        }

        $errors = [];
        $message = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name       = trim($_POST['name'] ?? '');
            $startDate  = trim($_POST['start_date'] ?? '');
            $endDate    = trim($_POST['end_date'] ?? '');
            $isActive   = isset($_POST['is_active']);

            if ($name === '' || $startDate === '' || $endDate === '') {
                $errors[] = 'Vui lòng nhập tên học kỳ, ngày bắt đầu và ngày kết thúc.';
            }

            if (empty($errors)) {
                try {
                    Semester::update($id, $name, $startDate, $endDate, $isActive);
                    $message  = 'Đã cập nhật học kỳ.';
                    $semester = Semester::findById($id);
                    $this->redirect('index.php?controller=admin_semester&action=index');
                } catch (Exception $e) {
                    $errors[] = 'Lỗi khi cập nhật học kỳ: ' . $e->getMessage();
                }
            }
        }

        $this->view('admin/semesters/edit', [
            'semester' => $semester,
            'errors'   => $errors,
            'message'  => $message,
        ], 'main');
    }

    public function setActive()
    {
        $this->requireRole(['admin']);

        $id  = (int)($_GET['id'] ?? 0);
        $act = (int)($_GET['act'] ?? 1);

        if ($id > 0) {
            Semester::setActive($id, $act === 1);
        }

        $this->redirect('index.php?controller=admin_semester&action=index');
    }
}
