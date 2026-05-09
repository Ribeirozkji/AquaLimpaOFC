<?php
session_start();
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método não permitido.');
}

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

if ($email === '' || $senha === '') {
    http_response_code(422);
    exit('Erro: informe e-mail e senha.');
}

$sql = 'SELECT id, nome, senha FROM usuario WHERE email = ? LIMIT 1';
$stmt = mysqli_prepare($conexao, $sql);
if (!$stmt) {
    error_log('Erro ao preparar login: ' . mysqli_error($conexao));
    http_response_code(500);
    exit('Erro interno. Tente novamente mais tarde.');
}

mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$user || !password_verify($senha, $user['senha'])) {
    http_response_code(401);
    exit('Credenciais inválidas.');
}

$_SESSION['usuario_id'] = $user['id'];
$_SESSION['usuario_nome'] = $user['nome'];

header('Location: ../index.html');
exit;
?>
