<?php
require 'conexao.php';
require_once __DIR__ . '/middlewares/SessionMiddleware.php';
require_once __DIR__ . '/middlewares/CsrfMiddleware.php';
require_once __DIR__ . '/middlewares/RateLimiter.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/utils/response.php';

startSecureSession();
$csrfToken = ensureCsrfToken();
header('X-CSRF-Token: ' . $csrfToken);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(405, ['ok' => false, 'message' => 'Método não permitido.']);
}

if (!verifyCsrfToken()) {
    jsonResponse(403, ['ok' => false, 'message' => 'CSRF token inválido.']);
}

$clientKey = ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . ':' . ($_POST['email'] ?? '');
if (!throttleLogin($clientKey)) {
    jsonResponse(429, ['ok' => false, 'message' => 'Muitas tentativas. Aguarde alguns minutos.']);
}

$userRepository = new UserRepository($conexao);
$service = new AuthService($userRepository);
$controller = new AuthController($service);
$controller->login($_POST);
?>
