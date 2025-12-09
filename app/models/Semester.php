<?php
// app/models/Semester.php

class Semester
{
    public static function allActive(): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->query("SELECT * FROM semesters WHERE is_active = 1 ORDER BY start_date DESC");
        return $stmt->fetchAll();
    }

    public static function all(): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->query("SELECT * FROM semesters ORDER BY is_active DESC, start_date DESC");
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM semesters WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(string $name, string $startDate, string $endDate, bool $active): void
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("
            INSERT INTO semesters (name, start_date, end_date, is_active)
            VALUES (:name, :start_date, :end_date, :is_active)
        ");
        $stmt->execute([
            ':name'       => $name,
            ':start_date' => $startDate,
            ':end_date'   => $endDate,
            ':is_active'  => $active ? 1 : 0,
        ]);
    }

    public static function update(int $id, string $name, string $startDate, string $endDate, bool $active): void
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("
            UPDATE semesters
            SET name = :name,
                start_date = :start_date,
                end_date   = :end_date,
                is_active  = :is_active
            WHERE id = :id
        ");
        $stmt->execute([
            ':name'       => $name,
            ':start_date' => $startDate,
            ':end_date'   => $endDate,
            ':is_active'  => $active ? 1 : 0,
            ':id'         => $id,
        ]);
    }

    public static function setActive(int $id, bool $active): void
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("UPDATE semesters SET is_active = :act WHERE id = :id");
        $stmt->execute([
            ':act' => $active ? 1 : 0,
            ':id'  => $id,
        ]);
    }
}
