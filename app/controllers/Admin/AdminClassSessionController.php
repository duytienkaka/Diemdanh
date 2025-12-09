<?php
// app/controllers/Admin/ClassSessionController.php

class AdminClassSessionController extends Controller
{
    public function createSchedule()
    {
        $this->requireRole(['admin']);

        $semesters = Semester::allActive();
        $subjects  = Subject::allActive();
        $message   = null;
        $errors    = [];

        // Giá trị cũ để fill lại form nếu submit lỗi
        $old = [
            'semester_id'  => $_POST['semester_id']  ?? '',
            'subject_id'   => $_POST['subject_id']   ?? '',
            'start_date'   => $_POST['start_date']   ?? '',
            'end_date'     => $_POST['end_date']     ?? '',
            'start_time'   => $_POST['start_time']   ?? '',
            'end_time'     => $_POST['end_time']     ?? '',
            'room'         => $_POST['room']         ?? '',
            'days_of_week' => $_POST['days_of_week'] ?? [],   // 👈 LƯU LẠI THỨ
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $semesterId = (int)($_POST['semester_id'] ?? 0);
            $subjectId  = (int)($_POST['subject_id'] ?? 0);
            $startDate  = trim($_POST['start_date'] ?? '');
            $endDate    = trim($_POST['end_date'] ?? '');
            $startTime  = trim($_POST['start_time'] ?? '');
            $endTime    = trim($_POST['end_time'] ?? '');
            $room       = trim($_POST['room'] ?? '');
            $daysOfWeek = $_POST['days_of_week'] ?? [];

            if ($semesterId <= 0) {
                $errors[] = 'Vui lòng chọn học kỳ.';
            }
            if ($subjectId <= 0) {
                $errors[] = 'Vui lòng chọn môn học.';
            }
            if ($startDate === '' || $endDate === '') {
                $errors[] = 'Vui lòng chọn ngày bắt đầu và kết thúc.';
            }
            if ($startTime === '' || $endTime === '') {
                $errors[] = 'Vui lòng nhập giờ bắt đầu và kết thúc.';
            }
            if (empty($daysOfWeek)) {
                $errors[] = 'Vui lòng chọn ít nhất một thứ trong tuần.';
            }

            // Chuẩn hoá daysOfWeek sang int nhưng vẫn giữ lại trong $old để hiển thị
            $daysOfWeek = array_map('intval', $daysOfWeek);
            $old['days_of_week'] = $daysOfWeek;

            if (empty($errors)) {
                try {
                    $count = ClassSession::createSchedule(
                        $semesterId,
                        $subjectId,
                        $startDate,
                        $endDate,
                        $daysOfWeek,
                        $startTime . ':00',
                        $endTime . ':00',
                        $room !== '' ? $room : null,
                        $_SESSION['user_id']
                    );

                    $message = "Đã tạo $count buổi học cho môn đã chọn.";
                    $this->redirect('index.php?controller=admin_class_session&action=index');
                } catch (Exception $e) {
                    $errors[] = 'Lỗi khi tạo thời khóa biểu: ' . $e->getMessage();
                }
            }
        }
        $weekdays = [
            2  => 'Thứ 2',
            3  => 'Thứ 3',
            4  => 'Thứ 4',
            5  => 'Thứ 5',
            6  => 'Thứ 6',
            7  => 'Thứ 7',
            8  => 'Chủ nhật',
        ];

        $this->view('admin/class_sessions/create_schedule', [
            'semesters'          => $semesters,
            'subjects'           => $subjects,
            'subjectsBySemester' => Subject::groupBySemester(),
            'message'            => $message,
            'errors'             => $errors,
            'old'                => $old,
            'weekdays'           => $weekdays,
        ], 'main');
    }

    public function index()
    {
        $this->requireRole(['admin']);

        $semesters = Semester::allActive();
        $subjects  = Subject::allActive();

        $semesterId = isset($_GET['semester_id']) ? (int)$_GET['semester_id'] : null;
        $subjectId  = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : null;
        $date       = isset($_GET['date']) ? trim($_GET['date']) : null;

        $sessions = ClassSession::search($semesterId, $subjectId, $date);

        $this->view('admin/class_sessions/index', [
            'semesters'   => $semesters,
            'subjects'    => $subjects,
            'sessions'    => $sessions,
            'semesterId'  => $semesterId,
            'subjectId'   => $subjectId,
            'date'        => $date,
        ], 'main');
    }

    // 🔹 NEW: Đổi trạng thái buổi học (scheduled/ongoing/ended)
    public function changeStatus()
    {
        $this->requireRole(['admin']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id     = (int)($_POST['id'] ?? 0);
            $status = $_POST['status'] ?? '';

            if ($id > 0 && in_array($status, ['scheduled', 'ongoing', 'ended'], true)) {
                ClassSession::updateStatus($id, $status);
            }
        }

        // Quay lại trang danh sách, giữ lại filter nếu có
        $query = [];
        if (isset($_GET['semester_id'])) $query[] = 'semester_id=' . urlencode($_GET['semester_id']);
        if (isset($_GET['subject_id']))  $query[] = 'subject_id=' . urlencode($_GET['subject_id']);
        if (isset($_GET['date']))        $query[] = 'date=' . urlencode($_GET['date']);

        $queryString = !empty($query) ? '&' . implode('&', $query) : '';

        $this->redirect('index.php?controller=admin_class_session&action=index' . $queryString);
    }
    public function createMakeup()
    {
        $this->requireRole(['admin']);

        // chỉ cần học kỳ, môn sẽ load bằng AJAX
        $semesters = Semester::allActive(); // hoặc Semester::all() tùy bạn đang dùng
        $errors    = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $semesterId = (int)($_POST['semester_id'] ?? 0);
            $subjectId  = (int)($_POST['subject_id'] ?? 0);
            $date       = trim($_POST['date'] ?? '');
            $startTime  = trim($_POST['start_time'] ?? '');
            $endTime    = trim($_POST['end_time'] ?? '');
            $room       = trim($_POST['room'] ?? '');

            if ($semesterId <= 0) $errors[] = 'Vui lòng chọn học kỳ.';
            if ($subjectId  <= 0) $errors[] = 'Vui lòng chọn môn học.';
            if ($date === '')     $errors[] = 'Vui lòng chọn ngày buổi bù.';
            if ($startTime === '' || $endTime === '') {
                $errors[] = 'Vui lòng nhập giờ bắt đầu và kết thúc.';
            }

            if (empty($errors)) {
                try {
                    ClassSession::createMakeup(
                        $semesterId,
                        $subjectId,
                        $date,
                        $startTime . ':00',
                        $endTime   . ':00',
                        $room !== '' ? $room : null,
                        $_SESSION['user_id']
                    );

                    // ✅ PRG: tạo xong thì quay về danh sách buổi học
                    $this->redirect('index.php?controller=admin_class_session&action=index');
                } catch (Exception $e) {
                    $errors[] = 'Lỗi khi tạo buổi bù: ' . $e->getMessage();
                }
            }
        }

        $this->view('admin/class_sessions/create_makeup', [
            'semesters' => $semesters,
            'errors'    => $errors,
        ], 'main');
    }

    public function changeStatusAjax()
    {
        $this->requireRole(['admin']);
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        $id     = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';

        if ($id <= 0 || !in_array($status, ['scheduled', 'ongoing', 'ended'], true)) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
            return;
        }

        $ok = ClassSession::updateStatus($id, $status);
        if ($ok) {
            echo json_encode(['success' => true, 'message' => 'Đã cập nhật trạng thái']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không thể cập nhật trạng thái']);
        }
    }
}
