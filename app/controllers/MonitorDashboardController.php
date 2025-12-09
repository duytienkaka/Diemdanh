<?php
// app/controllers/MonitorDashboardController.php

class MonitorDashboardController extends Controller
{
    public function index()
    {
        $this->requireRole(['monitor']);

        // Lấy sinh viên hiện tại
        $userId = (int)($_SESSION['user_id'] ?? 0);
        $student = Student::findByUserId($userId);

        if (!$student) {
            die('Tài khoản này chưa được gán với sinh viên nào.');
        }

        // Lấy danh sách sinh viên
        $allStudents = Student::allActive();
        $totalStudents = count($allStudents);

        // Lấy điểm danh hôm nay
        $today = date('Y-m-d');
        $sessions = ClassSession::getSessionsWithAttendance($today);
        
        $presentCount = 0;
        $absentCount = 0;
        
        foreach ($sessions as $session) {
            $attendance = AttendanceRecord::getBySession($session['id']);
            foreach ($attendance as $record) {
                if ($record['status'] === 'present') {
                    $presentCount++;
                } else {
                    $absentCount++;
                }
            }
        }

        $this->view('monitor/dashboard', [
            'totalStudents' => $totalStudents,
            'presentToday' => $presentCount,
            'absentToday' => $absentCount,
        ], 'main');
    }
}
