<?php
// app/models/AttendanceRecord.php

class AttendanceRecord
{
    // Tạo record mặc định present cho tất cả sinh viên nếu chưa có
    public static function ensureForSession(int $sessionId): void
    {
        $pdo = Database::getInstance();

        // Lấy danh sách sinh viên active
        $students = Student::allActive();
        if (empty($students)) {
            return;
        }

        // Kiểm tra xem đã có record cho buổi này chưa
        $stmtCheck = $pdo->prepare("
            SELECT student_id 
            FROM attendance_records 
            WHERE class_session_id = :sid
        ");
        $stmtCheck->execute([':sid' => $sessionId]);
        $existing = $stmtCheck->fetchAll();

        $existingIds = array_column($existing, 'student_id');

        $stmtInsert = $pdo->prepare("
            INSERT INTO attendance_records 
            (class_session_id, student_id, status, marked_by, marked_at)
            VALUES (:sid, :student_id, 'present', NULL, NULL)
        ");

        foreach ($students as $st) {
            if (!in_array($st['id'], $existingIds)) {
                $stmtInsert->execute([
                    ':sid'        => $sessionId,
                    ':student_id' => $st['id'],
                ]);
            }
        }
    }
    public static function getBySession(int $sessionId): array
    {
        $pdo = Database::getInstance();
        $sql = "SELECT ar.*, st.full_name, st.student_code
                FROM attendance_records ar
                JOIN students st ON ar.student_id = st.id
                WHERE ar.class_session_id = :sid
                ORDER BY st.full_name ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':sid' => $sessionId]);

        return $stmt->fetchAll();
    }

    public static function updateForSession(int $sessionId, array $statuses, int $markedBy): void
    {
        $pdo = Database::getInstance();

        $allowed = ['present', 'late', 'truant', 'absent'];
        $stmt = $pdo->prepare("SELECT student_id FROM attendance_records WHERE session_id = :sid");
        $stmt->execute([':sid' => $sessionId]);
        $studentIds = array_column($stmt->fetchAll(), 'student_id');

        $pdo->beginTransaction();
        try {
            $upd = $pdo->prepare("
            UPDATE attendance_records
            SET status = :status, marked_by = :marked_by, updated_at = NOW()
            WHERE session_id = :session_id AND student_id = :student_id
        ");

            foreach ($studentIds as $stId) {
                $stId = (int)$stId;
                $stStatus = $statuses[$stId] ?? 'present';

                if (!in_array($stStatus, $allowed, true)) {
                    $stStatus = 'present';
                }

                $upd->execute([
                    ':status'     => $stStatus,
                    ':marked_by'  => $markedBy,
                    ':session_id' => $sessionId,
                    ':student_id' => $stId,
                ]);
            }

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function getTodayForStudent(int $studentId): ?array
    {
        $pdo = Database::getInstance();
        $today = date('Y-m-d');

        $sql = "SELECT ar.*, cs.session_date, cs.start_time, cs.end_time,
                       cs.status AS session_status,
                       s.name AS subject_name, s.code AS subject_code,
                       sem.name AS semester_name
                FROM attendance_records ar
                JOIN class_sessions cs ON ar.class_session_id = cs.id
                JOIN subjects s ON cs.subject_id = s.id
                JOIN semesters sem ON cs.semester_id = sem.id
                WHERE ar.student_id = :sid
                  AND cs.session_date = :today
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':sid'   => $studentId,
            ':today' => $today,
        ]);

        $row = $stmt->fetch();
        return $row ?: null;
    }

    // 🔹 NEW: lịch sử điểm danh của sinh viên (có thể filter theo học kỳ sau này)
    public static function getHistoryForStudent(int $studentId): array
    {
        $pdo = Database::getInstance();

        $sql = "SELECT ar.*, cs.session_date, cs.start_time, cs.end_time,
                       cs.is_makeup,
                       s.name AS subject_name, s.code AS subject_code,
                       sem.name AS semester_name
                FROM attendance_records ar
                JOIN class_sessions cs ON ar.class_session_id = cs.id
                JOIN subjects s ON cs.subject_id = s.id
                JOIN semesters sem ON cs.semester_id = sem.id
                WHERE ar.student_id = :sid
                ORDER BY cs.session_date DESC, cs.start_time DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':sid' => $studentId]);

        return $stmt->fetchAll();
    }
    // 🔹 Tổng số buổi vắng của từng sinh viên
    public static function getAbsenceSummary(string $startDate = null, string $endDate = null): array
    {
        $pdo = Database::getInstance();

        // Nếu có lọc theo ngày, join với class_sessions để lọc theo cs.session_date
        $params = [];
        $dateFilter = '';
        if (!empty($startDate) && !empty($endDate)) {
            $dateFilter = "AND cs.session_date BETWEEN :start_date AND :end_date";
            $params[':start_date'] = $startDate;
            $params[':end_date'] = $endDate;
        } elseif (!empty($startDate)) {
            $dateFilter = "AND cs.session_date >= :start_date";
            $params[':start_date'] = $startDate;
        } elseif (!empty($endDate)) {
            $dateFilter = "AND cs.session_date <= :end_date";
            $params[':end_date'] = $endDate;
        }

        $sql = "SELECT st.id AS student_id, st.student_code, st.full_name,
                       COUNT(*) AS total_absent
                FROM attendance_records ar
                JOIN students st ON ar.student_id = st.id
                JOIN class_sessions cs ON ar.class_session_id = cs.id
                WHERE ar.status = 'absent' " . $dateFilter . "
                GROUP BY st.id, st.student_code, st.full_name
                ORDER BY total_absent DESC, st.full_name ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // 🔹 Chi tiết các buổi vắng của 1 sinh viên
    public static function getAbsenceDetailForStudent(int $studentId, string $startDate = null, string $endDate = null): array
    {
        $pdo = Database::getInstance();
        $params = [':sid' => $studentId];
        $dateFilter = '';
        if (!empty($startDate) && !empty($endDate)) {
            $dateFilter = "AND cs.session_date BETWEEN :start_date AND :end_date";
            $params[':start_date'] = $startDate;
            $params[':end_date'] = $endDate;
        } elseif (!empty($startDate)) {
            $dateFilter = "AND cs.session_date >= :start_date";
            $params[':start_date'] = $startDate;
        } elseif (!empty($endDate)) {
            $dateFilter = "AND cs.session_date <= :end_date";
            $params[':end_date'] = $endDate;
        }

        $sql = "SELECT cs.session_date, cs.start_time, cs.end_time,
                       cs.is_makeup,
                       s.name AS subject_name, s.code AS subject_code,
                       sem.name AS semester_name
                FROM attendance_records ar
                JOIN class_sessions cs ON ar.class_session_id = cs.id
                JOIN subjects s ON cs.subject_id = s.id
                JOIN semesters sem ON cs.semester_id = sem.id
                WHERE ar.student_id = :sid
                  AND ar.status = 'absent' " . $dateFilter . "
                ORDER BY cs.session_date DESC, cs.start_time DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
}
