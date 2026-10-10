<?php
/**
 * Admin security helpers.
 * - Rate limiting
 * - Login logging
 * - 2FA code generation and verification
 */

require_once __DIR__ . '/mailer.php';

/* =========================================================
   PATHS
   ========================================================= */

if (!defined('SECURITY_DATA_DIR')) {
    define('SECURITY_DATA_DIR', dirname(__DIR__) . '/data');
}

function security_attempts_dir(): string
{
    $dir = SECURITY_DATA_DIR . '/login_attempts';
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    return $dir;
}

function security_log_file(): string
{
    return SECURITY_DATA_DIR . '/login_log.txt';
}


/* =========================================================
   IP ADDRESS
   ========================================================= */

function security_client_ip(): string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    /* Best-effort: honour proxy headers if present */
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $first = trim($parts[0]);
        if (filter_var($first, FILTER_VALIDATE_IP)) {
            $ip = $first;
        }
    } elseif (!empty($_SERVER['HTTP_X_REAL_IP']) && filter_var($_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }

    return $ip;
}


/* =========================================================
   RATE LIMITING
   ========================================================= */

const RATE_MAX_ATTEMPTS = 5;         // failed attempts allowed
const RATE_WINDOW_MINUTES = 15;      // within this many minutes


function security_attempt_file(): string
{
    $ip = preg_replace('/[^a-f0-9\.:]/i', '_', security_client_ip());
    return security_attempts_dir() . '/' . md5($ip) . '.json';
}


function security_get_attempts(): array
{
    $file = security_attempt_file();
    if (!is_file($file)) {
        return ['count' => 0, 'first' => 0, 'last' => 0];
    }

    $data = json_decode((string)file_get_contents($file), true);
    if (!is_array($data)) {
        return ['count' => 0, 'first' => 0, 'last' => 0];
    }

    /* Expire window */
    $windowSeconds = RATE_WINDOW_MINUTES * 60;
    if (!empty($data['first']) && (time() - (int)$data['first']) > $windowSeconds) {
        @unlink($file);
        return ['count' => 0, 'first' => 0, 'last' => 0];
    }

    return $data;
}


function security_is_locked_out(): bool
{
    $data = security_get_attempts();
    return ($data['count'] ?? 0) >= RATE_MAX_ATTEMPTS;
}


function security_lockout_remaining_minutes(): int
{
    $data = security_get_attempts();
    if (empty($data['first'])) return 0;

    $windowSeconds = RATE_WINDOW_MINUTES * 60;
    $elapsed = time() - (int)$data['first'];
    $remaining = $windowSeconds - $elapsed;

    return $remaining > 0 ? (int)ceil($remaining / 60) : 0;
}


function security_record_failed_attempt(): void
{
    $file = security_attempt_file();
    $data = security_get_attempts();
    $now  = time();

    if (empty($data['first'])) $data['first'] = $now;
    $data['count'] = ((int)($data['count'] ?? 0)) + 1;
    $data['last']  = $now;

    @file_put_contents($file, json_encode($data));
}


function security_clear_attempts(): void
{
    @unlink(security_attempt_file());
}


/* =========================================================
   LOGGING
   ========================================================= */

function security_log(string $event, string $detail = ''): void
{
    $file = security_log_file();
    $line = sprintf(
        "[%s] %s | ip=%s | ua=%s | %s\n",
        date('Y-m-d H:i:s'),
        $event,
        security_client_ip(),
        substr($_SERVER['HTTP_USER_AGENT'] ?? '-', 0, 80),
        $detail
    );
    @file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
}


/* =========================================================
   2FA — CODE GENERATION + EMAIL
   ========================================================= */

const TWO_FA_CODE_TTL = 600;  // 10 minutes


function security_generate_2fa_code(): string
{
    return str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}


/**
 * Send the 2FA code to the admin's Gmail address.
 * Returns true on success.
 */
function security_send_2fa_code(string $code): bool
{
    try {
        $mail = fete_mailer();
        $mail->addAddress(FETE_SMTP_TO);

        $mail->Subject = 'Your FeteCation admin login code';

        $mail->Body =
              "Your admin login verification code is:\n\n"
            . "   {$code}\n\n"
            . "This code expires in 10 minutes.\n\n"
            . "If you didn't try to log in, someone may have your password —\n"
            . "change it immediately.\n\n"
            . "— FeteCation Security\n";

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log('2FA email failed: ' . $mail->ErrorInfo);
        return false;
    }
}