<?php
// app/models/Subject.php

class Subject
{
    /**
     * Lấy các môn đang active.
     * - Nếu $semesterId = null: lấy tất cả môn active.
     * - Nếu có $semesterId: lấy môn active thuộc học kỳ đó (dùng bảng subject_semesters).
     */
    public static function allActive(?int $semesterId = null): array
    {
        $pdo = Database::getInstance();

        // Không lọc theo học kỳ
        if ($semesterId === null || $semesterId <= 0) {
            $stmt = $pdo->query("SELECT * FROM subjects WHERE is_active = 1 ORDER BY name ASC");
            return $stmt->fetchAll();
        }

        // Có lọc theo học kỳ → dùng bảng mapping subject_semesters
        try {
            $sql = "SELECT s.*, sem.id AS semester_id, sem.name AS semester_name
                    FROM subject_semesters ss
                    JOIN subjects s ON s.id = ss.subject_id
                    JOIN semesters sem ON sem.id = ss.semester_id
                    WHERE s.is_active = 1
                      AND ss.semester_id = :sem_id
                    ORDER BY s.name ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':sem_id' => $semesterId]);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            // Nếu bảng subject_semesters chưa tồn tại → fallback
            $stmt = $pdo->query("SELECT * FROM subjects WHERE is_active = 1 ORDER BY name ASC");
            return $stmt->fetchAll();
        }
    }

    /**
     * Lấy danh sách môn (có thể lọc theo học kỳ).
     * - Nếu không lọc: lấy tất cả với thông tin học kỳ.
     * - Nếu có $semesterId: lấy môn thuộc học kỳ đó.
     */
    public static function all(?int $semesterId = null): array
    {
        $pdo = Database::getInstance();

        // Có lọc theo học kỳ
        if ($semesterId !== null && $semesterId > 0) {
            $sql = "SELECT 
                        s.id, s.code, s.name, s.description, s.semester_id, s.is_active, s.created_at, s.updated_at,
                        sem.id AS sem_id, sem.name AS semester_name
                    FROM subjects s
                    LEFT JOIN semesters sem ON s.semester_id = sem.id
                    WHERE s.semester_id = :sem_id
                    ORDER BY s.is_active DESC, s.name ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':sem_id' => $semesterId]);
            return $stmt->fetchAll();
        }

        // Không lọc theo học kỳ → lấy tất cả với thông tin học kỳ
        $sql = "SELECT 
                    s.id, s.code, s.name, s.description, s.semester_id, s.is_active, s.created_at, s.updated_at,
                    sem.id AS sem_id, sem.name AS semester_name
                FROM subjects s
                LEFT JOIN semesters sem ON s.semester_id = sem.id
                ORDER BY s.is_active DESC, s.name ASC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    }
    public static function allActiveBySemester(int $semesterId): array
    {
        $pdo = Database::getInstance();

        $sql = "SELECT s.*
            FROM subjects s
            WHERE s.is_active = 1
              AND s.semester_id = :sem_id
            ORDER BY s.name ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':sem_id' => $semesterId]);

        return $stmt->fetchAll();
    }
    public static function forSemester(int $semesterId): array
    {
        $pdo = Database::getInstance();
        if ($semesterId <= 0) {
            return self::allActive();
        }

        try {
            $stmt = $pdo->prepare("
                SELECT s.*, sem.id AS semester_id, sem.name AS semester_name
                FROM subject_semesters ss
                JOIN subjects s ON s.id = ss.subject_id
                JOIN semesters sem ON sem.id = ss.semester_id
                WHERE ss.semester_id = :sem_id
                  AND s.is_active = 1
                ORDER BY s.name ASC
            ");
            $stmt->execute([':sem_id' => $semesterId]);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            // Nếu chưa có bảng subject_semesters → fallback
            return self::allActive();
        }
    }

    /**
     * Group các môn theo học kỳ → trả về dạng:
     * [
     *   semester_id => [subject1, subject2, ...],
     *   ...
     * ]
     */
    public static function groupBySemester(): array
    {
        $pdo = Database::getInstance();
        try {
            $stmt = $pdo->query("
                SELECT ss.semester_id, s.*
                FROM subject_semesters ss
                JOIN subjects s ON s.id = ss.subject_id
                WHERE s.is_active = 1
                ORDER BY ss.semester_id, s.name
            ");

            $rows = $stmt->fetchAll();
            $out = [];

            foreach ($rows as $r) {
                $sid = $r['semester_id'];
                if (!isset($out[$sid])) {
                    $out[$sid] = [];
                }
                $subject = $r;
                // semester_id là của bảng mapping, không phải field của subjects
                unset($subject['semester_id']);
                $out[$sid][] = $subject;
            }

            return $out;
        } catch (\PDOException $e) {
            // Nếu không có bảng mapping thì trả về mảng rỗng
            return [];
        }
    }

    public static function findById(int $id): ?array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM subjects WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(string $code, string $name, ?string $description, int $semesterId): void
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("
            INSERT INTO subjects (code, name, description, semester_id, is_active)
            VALUES (:code, :name, :description, :semester_id, 1)
        ");
        $stmt->execute([
            ':code'        => $code,
            ':name'        => $name,
            ':description' => $description,
            ':semester_id' => $semesterId,
        ]);
    }

    public static function update(
        int $id,
        string $code,
        string $name,
        ?string $description,
        int $semesterId
    ): void {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("
        UPDATE subjects
        SET code        = :code,
            name        = :name,
            description = :description,
            semester_id = :semester_id
        WHERE id = :id
    ");
        $stmt->execute([
            ':code'        => $code,
            ':name'        => $name,
            ':description' => $description,
            ':semester_id' => $semesterId,
            ':id'          => $id,
        ]);
    }


    public static function setActive(int $id, bool $active): void
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("UPDATE subjects SET is_active = :act WHERE id = :id");
        $stmt->execute([
            ':act' => $active ? 1 : 0,
            ':id'  => $id,
        ]);
    }
}
