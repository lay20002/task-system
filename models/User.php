<?php
// models/User.php

class User extends Model
{
    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        if (!$stmt) {
            throw new Exception("DB prepare failed (findByUsername): " . $this->db->error);
        }

        $stmt->bind_param("s", $username);
        if (!$stmt->execute()) {
            $err = $stmt->error;
            $stmt->close();
            throw new Exception("DB execute failed (findByUsername): " . $err);
        }

        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $user ?: null;
    }

    public function findByRememberToken(string $rawToken): ?array
    {
        $hash = hash('sha256', $rawToken);

        $stmt = $this->db->prepare("SELECT * FROM users WHERE remember_token = ?");
        if (!$stmt) {
            throw new Exception("DB prepare failed (findByRememberToken): " . $this->db->error);
        }

        $stmt->bind_param("s", $hash);
        if (!$stmt->execute()) {
            $err = $stmt->error;
            $stmt->close();
            throw new Exception("DB execute failed (findByRememberToken): " . $err);
        }

        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $user ?: null;
    }

    public function updateRememberToken(int $id, ?string $rawToken): bool
    {
        $hash = $rawToken === null ? null : hash('sha256', $rawToken);

        $stmt = $this->db->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
        if (!$stmt) {
            throw new Exception("DB prepare failed (updateRememberToken): " . $this->db->error);
        }

        $stmt->bind_param("si", $hash, $id);

        if (!$stmt->execute()) {
            $err = $stmt->error;
            $stmt->close();
            throw new Exception("DB execute failed (updateRememberToken): " . $err);
        }

        $stmt->close();
        return true;
    }

    public function updatePassword(int $id, string $newHashedPassword): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
        if (!$stmt) {
            throw new Exception("DB prepare failed (updatePassword): " . $this->db->error);
        }

        $stmt->bind_param("si", $newHashedPassword, $id);

        if (!$stmt->execute()) {
            $err = $stmt->error;
            $stmt->close();
            throw new Exception("DB execute failed (updatePassword): " . $err);
        }

        $stmt->close();
        return true;
    }

    public function all(): array
    {
        $result = $this->db->query("SELECT id, username, role FROM users");
        if (!$result) {
            throw new Exception("DB query failed (all): " . $this->db->error);
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function create(string $username, string $password, string $role): bool
    {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        if (!$stmt) {
            throw new Exception("DB prepare failed (create): " . $this->db->error);
        }

        $stmt->bind_param("sss", $username, $hashed, $role);

        if (!$stmt->execute()) {
            $err = $stmt->error;
            $stmt->close();
            throw new Exception("DB execute failed (create): " . $err);
        }

        $stmt->close();
        return true;
    }

    // ✅ NEW DELETE METHOD
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ? LIMIT 1");
        if (!$stmt) {
            throw new Exception("DB prepare failed (delete): " . $this->db->error);
        }

        $stmt->bind_param("i", $id);

        if (!$stmt->execute()) {
            $err = $stmt->error;
            $stmt->close();
            throw new Exception("DB execute failed (delete): " . $err);
        }

        $stmt->close();
        return true;
    }
}
