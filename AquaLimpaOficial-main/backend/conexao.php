<?php
$servidor = getenv('DB_HOST') ?: 'localhost';
$usuario = getenv('DB_USER') ?: 'root';
$senha = getenv('DB_PASS') ?: '';
$banco = getenv('DB_NAME') ?: 'AQUALIMPA';

mysqli_report(MYSQLI_REPORT_OFF);
$conexao = mysqli_connect($servidor, $usuario, $senha, $banco);

if (!$conexao) {
    error_log('Erro na conexão com o banco de dados: ' . mysqli_connect_error());
    http_response_code(500);
    exit('Erro interno. Tente novamente mais tarde.');
}
?>
