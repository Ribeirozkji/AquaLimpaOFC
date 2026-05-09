<?php
class UserRepository
{
    public function __construct(private mysqli $db)
    {
    }

    public function findByEmail(string $email): ?array
    {
        $sql = 'SELECT id, nome, email, senha FROM usuario WHERE email = ? LIMIT 1';
        $stmt = mysqli_prepare($this->db, $sql);
        if (!$stmt) {
            return null;
        }
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result) ?: null;
        mysqli_stmt_close($stmt);
        return $user;
    }

    public function create(string $nome, string $dtnasc, string $email, string $senhaHash): bool
    {
        $sql = 'INSERT INTO usuario (nome, dtnasc, email, senha) VALUES (?, ?, ?, ?)';
        $stmt = mysqli_prepare($this->db, $sql);
        if (!$stmt) {
            return false;
        }
        mysqli_stmt_bind_param($stmt, 'ssss', $nome, $dtnasc, $email, $senhaHash);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    public function ensureEmailUniqueIndex(): void
    {
        $sql = 'ALTER TABLE usuario ADD UNIQUE KEY uniq_usuario_email (email)';
        @mysqli_query($this->db, $sql);
    }
}
?>
