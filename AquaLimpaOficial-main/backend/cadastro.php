<?php
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $dtnasc = $_POST['data_nascimento'] ?? null;
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($nome === '' || $email === '' || $senha === '') {
        die('Erro: preencha os campos obrigatórios (nome, e-mail e senha).');
    }

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuario (nome, dtnasc, email, senha) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conexao, $sql);

    if (!$stmt) {
        die("Erro ao preparar cadastro: " . mysqli_error($conexao));
    }

    mysqli_stmt_bind_param($stmt, "ssss", $nome, $dtnasc, $email, $senhaHash);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../index.html");
        exit;
    }

    echo "Erro ao cadastrar: " . mysqli_error($conexao);
    mysqli_stmt_close($stmt);
}


?>
