<?php
require_once __DIR__ . '/middlewares/SessionMiddleware.php';
require_once __DIR__ . '/middlewares/CsrfMiddleware.php';
require_once __DIR__ . '/utils/response.php';

startSecureSession();
$token = ensureCsrfToken();
jsonResponse(200, ['ok' => true, 'csrf_token' => $token]);
?>
