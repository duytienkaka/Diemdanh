<?php
// app/models/ClassSession.php

class ClassSession
{
    // HÀM CŨ: tạo lịch nhiều buổi
    public static function createSchedule(
        int $semesterId,
        int $subjectId,
        string $startDate,
        string $endDate,
        array $daysOfWeek,
        string $startTime,
        string $endTime,
        ?string $room,
        int $createdBy
    ): int {
        $pdo = Database::getInstance();
        $pdo->beginTransaction();

        try {
            $current = new DateTime($startDate);
            $end     = new DateTime($endDate);

            $sql = "INSERT INTO class_sessions 
                    (semester_id, subject_id, session_date, start_time, end_time, room, status, is_makeup, is_attendance_done, created_by)
                    VALUES (:semester_id, :subject_id, :session_date, :start_time, :end_time, :room, 'scheduled', 0, 0, :created_by)";
            $stmt = $pdo->prepare($sql);

            $count = 0;

            while ($current <= $end) {
                $dayOfWeek = (int)$current->format('N');

                if (in_array($dayOfWeek, $daysOfWeek)) {
                    $stmt->execute([
                        ':semester_id'  => $semesterId,
                        ':subject_id'   => $subjectId,
                        ':session_date' => $current->format('Y-m-d'),
                        ':start_time'   => $startTime,
                        ':end_time'     => $endTime,
                        ':room'         => $room,
                        ':created_by'   => $createdBy,
                    ]);
                    $count++;
                }

                $current->modify('+1 day');
            }

            $pdo->commit();
            return $count;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    // 🔹 NEW: Lấy danh sách buổi học theo filter đơn giản
    public static function search(?int $semesterId = null, ?int $subjectId = null, ?string $date = null): array
    {
        $pdo = Database::getInstance();

        $sql = "SELECT 
                    cs.id, cs.semester_id, cs.subject_id, cs.session_date, cs.start_time, cs.end_time,
                    cs.room, cs.status, cs.is_makeup, cs.is_attendance_done, cs.created_by, cs.created_at, cs.updated_at,
                    s.name AS subject_name, s.code AS subject_code,
                    sem.id AS sem_id, sem.name AS semester_name
                FROM class_sessions cs
                LEFT JOIN subjects s ON cs.subject_id = s.id
                LEFT JOIN semesters sem ON cs.semester_id = sem.id
                WHERE 1=1";
        $params = [];

        if ($semesterId) {
            $sql .= " AND cs.semester_id = :semester_id";
            $params[':semester_id'] = $semesterId;
        }
        if ($subjectId) {
            $sql .= " AND cs.subject_id = :subject_id";
            $params[':subject_id'] = $subjectId;
        }
        if ($date) {
            $sql .= " AND cs.session_date = :session_date";
            $params[':session_date'] = $date;
        }

        $sql .= " ORDER BY cs.session_date DESC, cs.start_time ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    // 🔹 NEW: Đổi trạng thái buổi học
    public static function updateStatus(int $id, string $status): bool
    {
        $allowed = ['scheduled', 'ongoing', 'ended'];
        if (!in_array($status, $allowed, true)) {
            return false;
        }

        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("UPDATE class_sessions SET status = :status WHERE id = :id");
        return $stmt->execute([
            ':status' => $status,
            ':id'     => $id,
        ]);
    }
    // 🔹 NEW: Lấy các buổi học của HÔM NAY
    public static function getTodaySessions(): array
    {
        $pdo = Database::getInstance();
        $today = date('Y-m-d');

        $sql = "SELECT 
                    cs.id, cs.semester_id, cs.subject_id, cs.session_date, cs.start_time, cs.end_time,
                    cs.room, cs.status, cs.is_makeup, cs.is_attendance_done, cs.created_by, cs.created_at, cs.updated_at,
                    s.name AS subject_name, s.code AS subject_code,
                    sem.id AS sem_id, sem.name AS semester_name
                FROM class_sessions cs
                LEFT JOIN subjects s ON cs.subject_id = s.id
                LEFT JOIN semesters sem ON cs.semester_id = sem.id
                WHERE cs.session_date = :today
                ORDER BY cs.start_time ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':today' => $today]);

        return $stmt->fetchAll();
    }

    // 🔹 NEW: Đánh dấu buổi học đã điểm danh xong
    public static function markAttendanceDone(int $sessionId): void
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("
            UPDATE class_sessions
            SET is_attendance_done = 1
            WHERE id = :id
        ");
        $stmt->execute([':id' => $sessionId]);
    }
    // 🔹 NEW: lấy lịch học trong khoảng ngày (cho cả lớp, vì cả lớp học cùng)
    public static function getScheduleBetween(string $startDate, string $endDate): array
    {
        $pdo = Database::getInstance();

        $sql = "SELECT 
                    cs.id, cs.semester_id, cs.subject_id, cs.session_date, cs.start_time, cs.end_time,
                    cs.room, cs.status, cs.is_makeup, cs.is_attendance_done, cs.created_by, cs.created_at, cs.updated_at,
                    s.name AS subject_name, s.code AS subject_code,
                    sem.id AS sem_id, sem.name AS semester_name
                FROM class_sessions cs
                LEFT JOIN subjects s ON cs.subject_id = s.id
                LEFT JOIN semesters sem ON cs.semester_id = sem.id
                WHERE cs.session_date BETWEEN :start_date AND :end_date
                ORDER BY cs.session_date ASC, cs.start_time ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':start_date' => $startDate,
            ':end_date'   => $endDate,
        ]);

        return $stmt->fetchAll();
    }
    // Tạo 1 buổi học bù
    public static function createMakeup(
        int $semesterId,
        int $subjectId,
        string $date,
        string $startTime,
        string $endTime,
        ?string $room,
        int $createdBy
    ): void {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("
            INSERT INTO class_sessions
            (semester_id, subject_id, session_date, start_time, end_time, room, status, is_makeup, is_attendance_done, created_by)
            VALUES (:semester_id, :subject_id, :session_date, :start_time, :end_time, :room, 'scheduled', 1, 0, :created_by)
        ");
        $stmt->execute([
            ':semester_id'  => $semesterId,
            ':subject_id'   => $subjectId,
            ':session_date' => $date,
            ':start_time'   => $startTime,
            ':end_time'     => $endTime,
            ':room'         => $room,
            ':created_by'   => $createdBy,
        ]);
    }
    // Tự động cập nhật status dựa trên thời gian hiện tại
    public static function autoUpdateStatuses(): void
    {
        $pdo = Database::getInstance();

        // status = scheduled -> ongoing nếu đang trong khoảng giờ học hôm nay
        $sql1 = "UPDATE class_sessions
                 SET status = 'ongoing'
                 WHERE status = 'scheduled'
                   AND session_date = CURDATE()
                   AND TIME(NOW()) BETWEEN start_time AND end_time";
        $pdo->exec($sql1);

        // status = scheduled hoặc ongoing -> ended nếu đã qua giờ kết thúc hôm nay hoặc ngày quá khứ
        $sql2 = "UPDATE class_sessions
                 SET status = 'ended'
                 WHERE status IN ('scheduled', 'ongoing')
                   AND (
                        session_date < CURDATE()
                        OR (session_date = CURDATE() AND TIME(NOW()) > end_time)
                   )";
        $pdo->exec($sql2);
    }
    // Lấy thông tin 1 buổi học (kèm môn + học kỳ)
    public static function findWithInfo(int $id): ?array
    {
        $pdo = Database::getInstance();

        $sql = "SELECT cs.*, 
                       s.name AS subject_name, s.code AS subject_code,
                       sem.name AS semester_name
                FROM class_sessions cs
                JOIN subjects s ON cs.subject_id = s.id
                JOIN semesters sem ON cs.semester_id = sem.id
                WHERE cs.id = :id
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch();
        return $row ?: null;
    }

    // Lấy các buổi đã điểm danh xong (is_attendance_done = 1), có thể lọc theo ngày
    public static function getSessionsWithAttendance(?string $date = null): array
    {
        $pdo = Database::getInstance();

        $sql = "SELECT cs.*,
                       s.name AS subject_name, s.code AS subject_code,
                       sem.name AS semester_name
                FROM class_sessions cs
                JOIN subjects s ON cs.subject_id = s.id
                JOIN semesters sem ON cs.semester_id = sem.id
                WHERE cs.is_attendance_done = 1";
        $params = [];

        if ($date) {
            $sql .= " AND cs.session_date = :session_date";
            $params[':session_date'] = $date;
        }

        $sql .= " ORDER BY cs.session_date DESC, cs.start_time DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
}
