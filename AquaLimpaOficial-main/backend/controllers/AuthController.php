<?php
require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../utils/response.php';

class AuthController
{
    public function __construct(private AuthService $service)
    {
    }

    public function register(array $data): void
    {
        $nome = trim($data['nome'] ?? '');
        $dtnasc = trim($data['data_nascimento'] ?? '');
        $email = trim($data['email'] ?? '');
        $senha = $data['senha'] ?? '';

        if ($nome === '' || $dtnasc === '' || $email === '' || $senha === '') {
            jsonResponse(422, ['ok' => false, 'message' => 'Preencha todos os campos obrigatórios.']);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            jsonResponse(422, ['ok' => false, 'message' => 'E-mail inválido.']);
        }
        if (strlen($senha) < 8) {
            jsonResponse(422, ['ok' => false, 'message' => 'A senha deve ter no mínimo 8 caracteres.']);
        }

        [$ok, $message, $status] = $this->service->register($nome, $dtnasc, $email, $senha);
        jsonResponse($status, ['ok' => $ok, 'message' => $message]);
    }

    public function login(array $data): void
    {
        $email = trim($data['email'] ?? '');
        $senha = $data['senha'] ?? '';

        if ($email === '' || $senha === '') {
            jsonResponse(422, ['ok' => false, 'message' => 'Informe e-mail e senha.']);
        }

        [$ok, $message, $status, $user] = $this->service->login($email, $senha);
        if (!$ok) {
            jsonResponse($status, ['ok' => false, 'message' => $message]);
        }

        session_regenerate_id(true);
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_nome'] = $user['nome'];

        jsonResponse(200, ['ok' => true, 'message' => $message]);
    }
}
?>
