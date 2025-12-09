<?php
// app/controllers/AdminDashboardController.php

class AdminDashboardController extends Controller
{
    public function index()
    {
        $this->requireRole(['admin']);

        // Lấy thống kê từ database
        $students = Student::allActive();
        $subjects = Subject::all();
        $semesters = Semester::all();
        $sessions = ClassSession::search();  // Lấy TẤT CẢ buổi học, không filter
        $todaySessions = ClassSession::getTodaySessions();  // Lấy buổi học hôm nay

        $totalStudents = count($students);
        $totalSubjects = count($subjects);
        $totalSemesters = count($semesters);
        $totalSessions = count($sessions);

        $this->view('admin/dashboard', [
            'totalStudents' => $totalStudents,
            'totalSubjects' => $totalSubjects,
            'totalSemesters' => $totalSemesters,
            'totalSessions' => $totalSessions,
            'todaySessions' => $todaySessions,
        ], 'main');
    }
}
