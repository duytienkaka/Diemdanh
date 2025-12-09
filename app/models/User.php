<?php
// app/models/User.php

class User
{
    public static function findByUsername(string $username): ?array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u AND is_active = 1 LIMIT 1");
        $stmt->execute([':u' => $username]);

        $user = $stmt->fetch();
        return $user ?: null;
    }
}
