<?php
function throttleLogin(string $key, int $limit = 5, int $windowSeconds = 300): bool
{
    $dir = sys_get_temp_dir() . '/aqualimpa_rate_limit';
    if (!is_dir($dir)) {
        mkdir($dir, 0700, true);
    }

    $file = $dir . '/' . hash('sha256', $key) . '.json';
    $now = time();
    $attempts = [];

    if (file_exists($file)) {
        $attempts = json_decode((string) file_get_contents($file), true) ?: [];
    }

    $attempts = array_filter($attempts, fn ($ts) => ($now - (int) $ts) <= $windowSeconds);
    if (count($attempts) >= $limit) {
        return false;
    }

    $attempts[] = $now;
    file_put_contents($file, json_encode(array_values($attempts)));
    return true;
}
?>
