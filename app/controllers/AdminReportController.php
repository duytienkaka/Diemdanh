<?php
// app/controllers/AdminReportController.php

class AdminReportController extends Controller
{
    // Tổng hợp vắng
    public function index()
    {
        $this->requireRole(['admin']);

        $summary = AttendanceRecord::getAbsenceSummary();

        $this->view('admin/reports/absence_summary', [
            'summary' => $summary,
        ], 'main');
    }

    // Chi tiết vắng của 1 sinh viên
    public function studentDetail()
    {
        $this->requireRole(['admin']);

        $studentId = (int)($_GET['id'] ?? 0);
        if ($studentId <= 0) {
            die('Thiếu ID sinh viên.');
        }

        $student = Student::findById($studentId);
        if (!$student) {
            die('Không tìm thấy sinh viên.');
        }

        $records = AttendanceRecord::getAbsenceDetailForStudent($studentId);

        $this->view('admin/reports/absence_detail', [
            'student' => $student,
            'records' => $records,
        ], 'main');
    }
}
