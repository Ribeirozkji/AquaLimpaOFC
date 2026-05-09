<?php

require_once __DIR__ . '/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método não permitido.');
}

$nome = trim($_POST['nome'] ?? '');
$dtnasc = trim($_POST['data_nascimento'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

if ($nome === '' || $dtnasc === '' || $email === '' || $senha === '') {
    http_response_code(422);
    exit('Erro: preencha todos os campos obrigatórios.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    exit('Erro: e-mail inválido.');
}

if (strlen($senha) < 8) {
    http_response_code(422);
    exit('Erro: a senha deve ter no mínimo 8 caracteres.');
}

$checkSql = 'SELECT id FROM usuario WHERE email = ? LIMIT 1';
$checkStmt = mysqli_prepare($conexao, $checkSql);

if (!$checkStmt) {
    error_log('Erro ao preparar verificação de e-mail: ' . mysqli_error($conexao));
    http_response_code(500);
    exit('Erro interno. Tente novamente mais tarde.');
}

mysqli_stmt_bind_param($checkStmt, 's', $email);
mysqli_stmt_execute($checkStmt);
mysqli_stmt_store_result($checkStmt);

if (mysqli_stmt_num_rows($checkStmt) > 0) {
    mysqli_stmt_close($checkStmt);
    http_response_code(409);
    exit('Erro: e-mail já cadastrado.');
}

mysqli_stmt_close($checkStmt);

$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

$sql = 'INSERT INTO usuario (nome, dtnasc, email, senha) VALUES (?, ?, ?, ?)';
$stmt = mysqli_prepare($conexao, $sql);

if (!$stmt) {
    error_log('Erro ao preparar cadastro: ' . mysqli_error($conexao));
    http_response_code(500);
    exit('Erro interno. Tente novamente mais tarde.');
}

mysqli_stmt_bind_param($stmt, 'ssss', $nome, $dtnasc, $email, $senhaHash);

if (!mysqli_stmt_execute($stmt)) {
    error_log('Erro ao cadastrar usuário: ' . mysqli_error($conexao));
    mysqli_stmt_close($stmt);
    http_response_code(500);
    exit('Erro interno. Tente novamente mais tarde.');
}

mysqli_stmt_close($stmt);

header('Location: ../pages/login.html');
exit;