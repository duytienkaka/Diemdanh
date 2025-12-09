<?php

class AttendanceController extends Controller
{
    // 🔹 Điểm danh buổi học hôm nay cho Lớp trưởng
    public function today()
    {
        $this->requireRole(['monitor']);

        // Lấy danh sách các buổi học hôm nay
        $sessions = ClassSession::getTodaySessions();
        $message  = null;
        $errors   = [];

        if (empty($sessions)) {
            // Không có buổi học nào hôm nay
            $this->view('monitor/attendance/today', [
                'sessions'        => [],
                'selectedSession' => null,
                'attendanceList'  => [],
                'message'         => null,
                'errors'          => ['Hôm nay không có buổi học nào.'],
            ], 'main');
            return;
        }

        // Chọn buổi theo GET ?session_id=..., nếu không có thì lấy buổi đầu tiên
        $selectedSessionId = isset($_GET['session_id']) ? (int)$_GET['session_id'] : (int)$sessions[0]['id'];
        $selectedSession   = null;

        foreach ($sessions as $ses) {
            if ((int)$ses['id'] === $selectedSessionId) {
                $selectedSession = $ses;
                break;
            }
        }

        // Nếu không tìm thấy (vd: id linh tinh) thì fallback về buổi đầu
        if (!$selectedSession) {
            $selectedSession   = $sessions[0];
            $selectedSessionId = (int)$sessions[0]['id'];
        }

        // Nếu POST -> lưu điểm danh
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sessionIdPost = (int)($_POST['session_id'] ?? 0);
            $absentIds     = $_POST['absent_ids'] ?? [];

            // Đồng bộ selectedSessionId với POST
            $selectedSessionId = $sessionIdPost;

            // Tìm lại selectedSession dựa trên POST
            $selectedSession = null;
            foreach ($sessions as $ses) {
                if ((int)$ses['id'] === $sessionIdPost) {
                    $selectedSession = $ses;
                    break;
                }
            }

            if ($sessionIdPost <= 0 || !$selectedSession) {
                $errors[] = 'Buổi học không hợp lệ khi lưu điểm danh.';
            } else {
                // Không cho điểm danh nếu buổi đã kết thúc
                if ($selectedSession['status'] === 'ended') {
                    $errors[] = 'Buổi học đã kết thúc, không thể điểm danh.';
                } else {
                    try {
                        // Đảm bảo đã có record mặc định cho tất cả sinh viên
                        AttendanceRecord::ensureForSession($sessionIdPost);

                        // Cập nhật vắng / có mặt
                        AttendanceRecord::updateForSession($sessionIdPost, $absentIds, $_SESSION['user_id']);

                        // Đánh dấu buổi học này đã điểm danh xong
                        ClassSession::markAttendanceDone($sessionIdPost);

                        $message = 'Đã lưu điểm danh thành công.';
                        $this->redirect('index.php?controller=monitor_attendance&action=today&session_id=' . $sessionIdPost);
                    } catch (Exception $e) {
                        $errors[] = 'Lỗi khi lưu điểm danh: ' . $e->getMessage();
                    }
                }
            }
        }

        // Lấy danh sách điểm danh của buổi đã chọn
        $attendanceList = [];
        if ($selectedSessionId) {
            AttendanceRecord::ensureForSession($selectedSessionId);
            $attendanceList = AttendanceRecord::getBySession($selectedSessionId);
        }

        $this->view('monitor/attendance/today', [
            'sessions'        => $sessions,
            'selectedSession' => $selectedSession,
            'attendanceList'  => $attendanceList,
            'message'         => $message,
            'errors'          => $errors,
        ], 'main');
    }
    public function history()
    {
        $this->requireRole(['monitor']);

        $date = isset($_GET['date']) ? trim($_GET['date']) : null;
        if ($date === '') {
            $date = null;
        }

        $sessions = ClassSession::getSessionsWithAttendance($date);

        $this->view('monitor/attendance/history', [
            'sessions' => $sessions,
            'date'     => $date,
        ], 'main');
    }

    // 🔹 Xem chi tiết 1 buổi (view-only)
    public function viewSession()
    {
        $this->requireRole(['monitor']);

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            die('Thiếu ID buổi học.');
        }

        $session = ClassSession::findWithInfo($id);
        if (!$session) {
            die('Không tìm thấy buổi học.');
        }

        // Lấy danh sách điểm danh cho buổi này
        AttendanceRecord::ensureForSession($id); // đề phòng chưa có record
        $attendanceList = AttendanceRecord::getBySession($id);

        $this->view('monitor/attendance/view_session', [
            'session'        => $session,
            'attendanceList' => $attendanceList,
        ], 'main');
    }
}
