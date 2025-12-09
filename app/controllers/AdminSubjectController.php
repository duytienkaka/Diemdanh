<?php

class AdminSubjectController extends Controller
{
    public function index()
    {
        $this->requireRole(['admin']);

        $semesters = Semester::all(); // để hiển thị dropdown filter
        $semesterId = isset($_GET['semester_id']) ? (int)$_GET['semester_id'] : 0;

        $subjects = Subject::all($semesterId > 0 ? $semesterId : null);

        $this->view('admin/subjects/index', [
            'subjects'   => $subjects,
            'semesters'  => $semesters,
            'semesterId' => $semesterId,
        ], 'main');
    }

    public function create()
    {
        $this->requireRole(['admin']);

        $semesters = Semester::all();
        $errors = [];
        $message = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code        = trim($_POST['code'] ?? '');
            $name        = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $semesterId  = (int)($_POST['semester_id'] ?? 0);

            if ($code === '' || $name === '') {
                $errors[] = 'Vui lòng nhập mã môn và tên môn.';
            }
            if ($semesterId <= 0) {
                $errors[] = 'Vui lòng chọn học kỳ.';
            }

            if (empty($errors)) {
                try {
                    Subject::create($code, $name, $description ?: null, $semesterId);
                    $message = 'Đã tạo môn học mới.';
                    $this->redirect('index.php?controller=admin_subject&action=index');
                } catch (Exception $e) {
                    $errors[] = 'Lỗi khi tạo môn học: ' . $e->getMessage();
                }
            }
        }

        $this->view('admin/subjects/create', [
            'errors'    => $errors,
            'message'   => $message,
            'semesters' => $semesters,
        ], 'main');
    }

    public function edit()
    {
        $this->requireRole(['admin']);

        $id = (int)($_GET['id'] ?? 0);
        $subject = Subject::findById($id);
        if (!$subject) {
            die('Không tìm thấy môn học.');
        }

        $semesters = Semester::all();
        $errors = [];
        $message = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code        = trim($_POST['code'] ?? '');
            $name        = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $semesterId  = (int)($_POST['semester_id'] ?? 0);

            if ($code === '' || $name === '') {
                $errors[] = 'Vui lòng nhập mã môn và tên môn.';
            }
            if ($semesterId <= 0) {
                $errors[] = 'Vui lòng chọn học kỳ.';
            }

            if (empty($errors)) {
                try {
                    Subject::update(
                        $id,
                        $code,
                        $name,
                        $description !== '' ? $description : null,
                        $semesterId
                    );

                    $message  = 'Đã cập nhật môn học.';
                    $subject = Subject::findById($id);
                    $this->redirect('index.php?controller=admin_subject&action=index');
                } catch (Exception $e) {
                    $errors[] = 'Lỗi khi cập nhật môn học: ' . $e->getMessage();
                }
            }
        }

        $this->view('admin/subjects/edit', [
            'subject'   => $subject,
            'semesters' => $semesters,
            'errors'    => $errors,
            'message'   => $message,
        ], 'main');
    }

    public function listBySemesterAjax()
    {
        $this->requireRole(['admin']);
        header('Content-Type: application/json; charset=utf-8');

        $semesterId = (int)($_GET['semester_id'] ?? 0);
        if ($semesterId <= 0) {
            echo json_encode([]);
            return;
        }

        // lấy các môn active trong học kỳ đó
        $subjects = Subject::allActiveBySemester($semesterId);

        echo json_encode($subjects);
    }

    public function setActive()
    {
        $this->requireRole(['admin']);

        $id  = (int)($_GET['id'] ?? 0);
        $act = (int)($_GET['act'] ?? 1); // 1 = kích hoạt, 0 = ẩn

        if ($id > 0) {
            Subject::setActive($id, $act === 1);
        }

        // Giữ lại filter học kỳ (nếu có) để reload trang list cho đẹp
        $semesterId = isset($_GET['semester_id']) ? (int)$_GET['semester_id'] : 0;
        if ($semesterId > 0) {
            $this->redirect('index.php?controller=admin_subject&action=index&semester_id=' . $semesterId);
        } else {
            $this->redirect('index.php?controller=admin_subject&action=index');
        }
    }
}
