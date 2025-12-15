<?php
// app/controllers/AdminReportController.php

class AdminReportController extends Controller
{
    // Tổng hợp vắng
    public function index()
    {
        $this->requireRole(['admin']);

        // Lấy params lọc ngày nếu có
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;

        // validate date format Y-m-d
        $validateDate = function ($d) {
            if (empty($d)) return null;
            $dt = DateTime::createFromFormat('Y-m-d', $d);
            return $dt && $dt->format('Y-m-d') === $d ? $d : null;
        };

        $startDate = $validateDate($startDate);
        $endDate = $validateDate($endDate);

        $summary = AttendanceRecord::getAbsenceSummary($startDate, $endDate);

        $this->view('admin/reports/absence_summary', [
            'summary' => $summary,
            'start_date' => $startDate,
            'end_date' => $endDate,
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

        // Lọc ngày nếu có
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;

        $validateDate = function ($d) {
            if (empty($d)) return null;
            $dt = DateTime::createFromFormat('Y-m-d', $d);
            return $dt && $dt->format('Y-m-d') === $d ? $d : null;
        };

        $startDate = $validateDate($startDate);
        $endDate = $validateDate($endDate);

        $records = AttendanceRecord::getAbsenceDetailForStudent($studentId, $startDate, $endDate);

        $this->view('admin/reports/absence_detail', [
            'student' => $student,
            'records' => $records,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ], 'main');
    }
}
