<?php
// app/models/Student.php

class Student
{
     public static function allActive(): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->query("SELECT * FROM students WHERE is_active = 1 ORDER BY full_name ASC");
        return $stmt->fetchAll();
    }

    public static function findByUserId(int $userId): ?array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM students WHERE user_id = :uid AND is_active = 1 LIMIT 1");
        $stmt->execute([':uid' => $userId]);

        $st = $stmt->fetch();
        return $st ?: null;
    }

    public static function allWithUser(): array
    {
        $pdo = Database::getInstance();
        $sql = "SELECT st.*, u.username, u.role, u.is_active AS user_active
            FROM students st
            JOIN users u ON st.user_id = u.id
            ORDER BY st.is_active DESC, st.full_name ASC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array
    {
        $pdo = Database::getInstance();
        $sql = "SELECT st.*, u.username, u.role
                FROM students st
                JOIN users u ON st.user_id = u.id
                WHERE st.id = :id
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    // 🔹 Tạo user + student trong 1 transaction
    public static function createWithUser(
        string $username,
        string $plainPassword,
        string $studentCode,
        string $fullName,
        ?string $gender,
        ?string $phone,
        bool $isMonitor
    ): void {
        $pdo = Database::getInstance();
        $pdo->beginTransaction();

        try {
            $role = $isMonitor ? 'monitor' : 'student';
            $hash = password_hash($plainPassword, PASSWORD_DEFAULT);

            // Tạo user
            $stmtUser = $pdo->prepare("
                INSERT INTO users (username, password_hash, role, is_active)
                VALUES (:username, :hash, :role, 1)
            ");
            $stmtUser->execute([
                ':username' => $username,
                ':hash'     => $hash,
                ':role'     => $role,
            ]);
            $userId = (int)$pdo->lastInsertId();

            // Nếu là lớp trưởng mới -> hạ các lớp trưởng cũ xuống
            if ($isMonitor) {
                $pdo->exec("UPDATE students st
                            JOIN users u ON st.user_id = u.id
                            SET st.is_monitor = 0, u.role = 'student'
                            WHERE st.is_monitor = 1");
            }

            // Tạo student
            $stmtSt = $pdo->prepare("
                INSERT INTO students (user_id, student_code, full_name, gender, phone, is_monitor, is_active)
                VALUES (:user_id, :code, :name, :gender, :phone, :is_monitor, 1)
            ");
            $stmtSt->execute([
                ':user_id'    => $userId,
                ':code'       => $studentCode,
                ':name'       => $fullName,
                ':gender'     => $gender,
                ':phone'      => $phone,
                ':is_monitor' => $isMonitor ? 1 : 0,
            ]);

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    // 🔹 Cập nhật thông tin sinh viên (không đổi password)
    public static function updateWithUser(
        int $studentId,
        string $studentCode,
        string $fullName,
        ?string $gender,
        ?string $phone
    ): void {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("
            UPDATE students
            SET student_code = :code,
                full_name    = :name,
                gender       = :gender,
                phone        = :phone
            WHERE id = :id
        ");
        $stmt->execute([
            ':code'   => $studentCode,
            ':name'   => $fullName,
            ':gender' => $gender,
            ':phone'  => $phone,
            ':id'     => $studentId,
        ]);
    }

    public static function setActive(int $studentId, bool $active): void
    {
        $pdo = Database::getInstance();
        $pdo->beginTransaction();

        try {
            $st = self::findById($studentId);
            if (!$st) {
                $pdo->commit();
                return;
            }

            // Cập nhật trạng thái sinh viên
            $pdo->prepare("UPDATE students SET is_active = :act WHERE id = :id")
                ->execute([
                    ':act' => $active ? 1 : 0,
                    ':id'  => $studentId,
                ]);

            // Cập nhật trạng thái user (tài khoản đăng nhập)
            $pdo->prepare("UPDATE users SET is_active = :act WHERE id = :uid")
                ->execute([
                    ':act' => $active ? 1 : 0,
                    ':uid' => $st['user_id'],
                ]);

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    // 🔹 Đặt 1 sinh viên làm lớp trưởng
    public static function setMonitor(int $studentId): void
    {
        $pdo = Database::getInstance();
        $pdo->beginTransaction();

        try {
            $st = self::findById($studentId);
            if (!$st) {
                $pdo->commit();
                return;
            }

            // Hạ lớp trưởng cũ
            $pdo->exec("UPDATE students st
                        JOIN users u ON st.user_id = u.id
                        SET st.is_monitor = 0, u.role = 'student'
                        WHERE st.is_monitor = 1");

            // Nâng lớp trưởng mới
            $pdo->prepare("
                UPDATE students
                SET is_monitor = 1
                WHERE id = :id
            ")->execute([':id' => $studentId]);

            $pdo->prepare("
                UPDATE users
                SET role = 'monitor'
                WHERE id = :uid
            ")->execute([':uid' => $st['user_id']]);

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
