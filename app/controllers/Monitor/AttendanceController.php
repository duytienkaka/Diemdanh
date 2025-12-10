<?php

class AttendanceController extends Controller
{
    public function today()
    {
        $this->requireRole(['monitor']);

        // Lấy "hôm nay" theo múi giờ Việt Nam
        $now   = new DateTimeImmutable('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
        $today = $now->format('Y-m-d');

        // Lấy danh sách các buổi học NGÀY HÔM NAY
        // (gợi ý: bạn nên dùng ClassSession::getSessionsForDate($today) như mình đã nói)
        if (method_exists('ClassSession', 'getSessionsForDate')) {
            $sessions = ClassSession::getSessionsForDate($today);
        } else {
            // fallback: vẫn dùng hàm cũ nếu bạn chưa tạo getSessionsForDate
            $sessions = ClassSession::getTodaySessions();
        }

        $message = null;
        $errors  = [];

        if (empty($sessions)) {
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
        $selectedSessionId = isset($_GET['session_id'])
            ? (int)$_GET['session_id']
            : (int)$sessions[0]['id'];

        $selectedSession = null;
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

        // 👉 TÍNH TRẠNG THÁI THỰC TẾ (DỰA THEO GIỜ) CHO BUỔI ĐANG CHỌN
        $time  = $now->format('H:i:s');
        $start = $selectedSession['start_time'];
        $end   = $selectedSession['end_time'];

        // Nếu end_time <= start_time (trường hợp nhập 00:00) thì coi như kết thúc 23:59:59
        if ($end <= $start) {
            $end = '23:59:59';
        }

        $effectiveStatus = $selectedSession['status'];

        if ($selectedSession['session_date'] < $today) {
            $effectiveStatus = 'ended';
        } elseif ($selectedSession['session_date'] > $today) {
            $effectiveStatus = 'scheduled';
        } else {
            // Hôm nay
            if ($time < $start) {
                $effectiveStatus = 'scheduled'; // CHƯA ĐẾN GIỜ
            } elseif ($time >= $start && $time < $end) {
                $effectiveStatus = 'ongoing';   // ĐANG DIỄN RA
            } else {
                $effectiveStatus = 'ended';     // ĐÃ QUA GIỜ
            }
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
                // 👉 TÍNH LẠI TRẠNG THÁI THỰC TẾ CHO BUỔI ĐƯỢC POST
                $time  = $now->format('H:i:s');
                $start = $selectedSession['start_time'];
                $end   = $selectedSession['end_time'];
                if ($end <= $start) {
                    $end = '23:59:59';
                }

                $effectiveStatus = $selectedSession['status'];
                if ($selectedSession['session_date'] < $today) {
                    $effectiveStatus = 'ended';
                } elseif ($selectedSession['session_date'] > $today) {
                    $effectiveStatus = 'scheduled';
                } else {
                    if ($time < $start) {
                        $effectiveStatus = 'scheduled';
                    } elseif ($time >= $start && $time < $end) {
                        $effectiveStatus = 'ongoing';
                    } else {
                        $effectiveStatus = 'ended';
                    }
                }

                // ❌ CHẶN MỌI TRƯỜNG HỢP NGOÀI GIỜ (CẢ CHƯA ĐẾN GIỜ VÀ ĐÃ QUA GIỜ)
                if ($effectiveStatus !== 'ongoing') {
                    if ($effectiveStatus === 'scheduled') {
                        $errors[] = 'Chưa đến giờ học, không thể điểm danh.';
                    } else {
                        $errors[] = 'Buổi học đã kết thúc, không thể điểm danh.';
                    }
                } else {
                    // ✅ CHỈ TRONG KHOẢNG GIỜ HỌC MỚI ĐƯỢC LƯU
                    try {
                        AttendanceRecord::ensureForSession($sessionIdPost);
                        AttendanceRecord::updateForSession($sessionIdPost, $absentIds, $_SESSION['user_id']);
                        ClassSession::markAttendanceDone($sessionIdPost);

                        $message = 'Đã lưu điểm danh thành công.';
                        $this->redirect(
                            'index.php?controller=monitor_attendance&action=today&session_id=' . $sessionIdPost
                        );
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
