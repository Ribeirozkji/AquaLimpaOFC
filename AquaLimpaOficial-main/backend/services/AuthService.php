<?php
require_once __DIR__ . '/../repositories/UserRepository.php';

class AuthService
{
    public function __construct(private UserRepository $users)
    {
    }

    public function register(string $nome, string $dtnasc, string $email, string $senha): array
    {
        if ($this->users->findByEmail($email)) {
            return [false, 'E-mail já cadastrado.', 409];
        }

        $hash = password_hash($senha, PASSWORD_DEFAULT);
        if (!$this->users->create($nome, $dtnasc, $email, $hash)) {
            return [false, 'Erro ao cadastrar usuário.', 500];
        }

        return [true, 'Cadastro realizado com sucesso.', 201];
    }

    public function login(string $email, string $senha): array
    {
        $user = $this->users->findByEmail($email);
        if (!$user || !password_verify($senha, $user['senha'])) {
            return [false, 'Credenciais inválidas.', 401, null];
        }

        return [true, 'Login realizado com sucesso.', 200, $user];
    }
}
?>
