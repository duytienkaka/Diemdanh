<?php
// app/controllers/StudentDashboardController.php

class StudentDashboardController extends Controller
{
    // Lấy thông tin sinh viên tương ứng với user hiện tại
    private function getCurrentStudent(): array
    {
        $this->requireRole(['student']);

        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            die('Không xác định được tài khoản người dùng.');
        }

        $student = Student::findByUserId($userId);
        if (!$student) {
            die('Tài khoản này chưa được gán với sinh viên nào.');
        }

        return $student;
    }

    // 🔹 Trang dashboard: chỉ hiện menu cho sinh viên
    public function index()
    {
        $this->requireRole(['student']);
        
        $student = $this->getCurrentStudent();
        
        // Lấy số môn học
        $subjects = Subject::all();
        $totalSubjects = count($subjects);
        
        // Lấy lịch sử điểm danh
        $records = AttendanceRecord::getHistoryForStudent($student['id']);
        $presentCount = count(array_filter($records, fn($r) => $r['status'] === 'present'));
        $absentCount = count(array_filter($records, fn($r) => $r['status'] === 'absent'));
        
        $this->view('student/dashboard', [
            'totalSubjects' => $totalSubjects,
            'presentCount' => $presentCount,
            'absentCount' => $absentCount,
        ], 'main');
    }

    // 🔹 Xem lịch học 7 ngày tới
    public function schedule()
    {
        $this->getCurrentStudent(); // chỉ để đảm bảo đúng role + có sinh viên

        $today = new DateTime();
        $end   = (clone $today)->modify('+7 days');

        $startDate = $today->format('Y-m-d');
        $endDate   = $end->format('Y-m-d');

        $sessions = ClassSession::getScheduleBetween($startDate, $endDate);

        $this->view('student/schedule', [
            'sessions'  => $sessions,
            'startDate' => $startDate,
            'endDate'   => $endDate,
        ], 'main');
    }

    // 🔹 Xem điểm danh hôm nay của bản thân
    public function attendanceToday()
    {
        $student = $this->getCurrentStudent();

        $record = AttendanceRecord::getTodayForStudent($student['id']);

        $this->view('student/attendance_today', [
            'record' => $record,
        ], 'main');
    }

    // 🔹 Xem lịch sử điểm danh của bản thân
    public function history()
    {
        $student = $this->getCurrentStudent();

        $records = AttendanceRecord::getHistoryForStudent($student['id']);

        $this->view('student/attendance_history', [
            'records' => $records,
        ], 'main');
    }
}
