<?php
/**
 * Customer authentication — register, login, verify, reset, remember me.
 * Works with the existing includes/database.php ($pdo).
 */

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/mailer.php';


/* =========================================================
   DB ACCESSOR
   ========================================================= */

function db(): PDO
{
    global $pdo;
    return $pdo;
}


/* =========================================================
   SESSION — starts at include time
   ========================================================= */

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

function customer_session_start(): void
{
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        session_start();
    }
}

function customer_is_logged_in(): bool
{
    customer_session_start();
    return !empty($_SESSION['customer_id']);
}

function customer_current(): ?array
{
    if (!customer_is_logged_in()) return null;

    static $customer = null;
    if ($customer !== null) return $customer;

    try {
        $stmt = db()->prepare('SELECT * FROM customers WHERE CustomerID = ? AND DeletedAt IS NULL LIMIT 1');
        $stmt->execute([$_SESSION['customer_id']]);
        $row = $stmt->fetch();

        return $customer = $row ?: null;

    } catch (Throwable $e) {
        return null;
    }
}

function customer_logout(): void
{
    $customerId = isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : null;

    customer_session_start();
    unset($_SESSION['customer_id'], $_SESSION['customer_name']);
    if (!headers_sent()) {
        session_regenerate_id(true);
    }

    /* Also kill the remember-me cookie + DB token */
    customer_clear_remember_token($customerId);
}


/* =========================================================
   REGISTRATION
   ========================================================= */

function customer_register(string $first, string $last, string $email, string $phone, string $password): array
{
    $first = trim($first);
    $last  = trim($last);
    $email = strtolower(trim($email));
    $phone = trim($phone);

    if ($first === '' || $last === '' || $email === '' || $password === '') {
        return ['ok' => false, 'error' => 'Please fill in all required fields.'];
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['ok' => false, 'error' => 'Please enter a valid email address.'];
    }

    if (strlen($password) < 8) {
        return ['ok' => false, 'error' => 'Password must be at least 8 characters.'];
    }

    $pdo = db();

    $stmt = $pdo->prepare('SELECT CustomerID FROM customers WHERE Email = ? LIMIT 1');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return ['ok' => false, 'error' => 'An account with that email already exists.'];
    }

    $hash         = password_hash($password, PASSWORD_DEFAULT);
    $verifyToken  = bin2hex(random_bytes(32));
    $verifyExpiry = date('Y-m-d H:i:s', time() + 60 * 60 * 24 * 2);

    $stmt = $pdo->prepare(
        'INSERT INTO customers
            (FirstName, LastName, Phone, Email, Password, EmailVerified, VerifyToken, VerifyExpires)
         VALUES (?, ?, ?, ?, ?, 0, ?, ?)'
    );

    try {
        $stmt->execute([$first, $last, $phone, $email, $hash, $verifyToken, $verifyExpiry]);
    } catch (Throwable $e) {
        error_log('customer_register failed: ' . $e->getMessage());
        return ['ok' => false, 'error' => 'Could not create account. Please try again.'];
    }

    $id = (int)$pdo->lastInsertId();

    customer_send_verification_email([
        'id'    => $id,
        'name'  => $first . ' ' . $last,
        'email' => $email,
        'token' => $verifyToken,
    ]);

    send_admin_new_customer_notification([
        'name'         => $first . ' ' . $last,
        'email'        => $email,
        'phone'        => $phone,
        'submitted_at' => date('Y-m-d H:i:s'),
    ]);

    return ['ok' => true, 'id' => $id];
}


/* =========================================================
   LOGIN
   ========================================================= */

function customer_login(string $email, string $password): array
{
    $email = strtolower(trim($email));

    if ($email === '' || $password === '') {
        return ['ok' => false, 'error' => 'Please enter your email and password.'];
    }

    $pdo = db();
    $stmt = $pdo->prepare('SELECT * FROM customers WHERE Email = ? LIMIT 1');
    $stmt->execute([$email]);
    $customer = $stmt->fetch();

    if (!$customer || !password_verify($password, $customer['Password'])) {
        usleep(400000);
        return ['ok' => false, 'error' => 'Invalid email or password.'];
    }

    if (!empty($customer['DeletedAt'])) {
        return ['ok' => false, 'error' => 'This account has been deleted.'];
    }

    try {
        $upd = $pdo->prepare('UPDATE customers SET LastLoginAt = NOW() WHERE CustomerID = ?');
        $upd->execute([$customer['CustomerID']]);
    } catch (Throwable $e) { /* non-fatal */ }

    customer_session_start();
    if (!headers_sent()) {
        session_regenerate_id(true);
    }

    $_SESSION['customer_id']   = (int)$customer['CustomerID'];
    $_SESSION['customer_name'] = $customer['FirstName'];

    /* Remember me? */
    if (!empty($_POST['remember'])) {
        customer_issue_remember_token((int)$customer['CustomerID']);
    } else {
        customer_clear_remember_token((int)$customer['CustomerID']);
    }

    return ['ok' => true];
}


/* =========================================================
   EMAIL VERIFICATION
   ========================================================= */

function customer_verify_email(string $token): bool
{
    $token = trim($token);
    if ($token === '') return false;

    $pdo = db();
    $stmt = $pdo->prepare(
        'SELECT CustomerID FROM customers
         WHERE VerifyToken = ?
           AND EmailVerified = 0
           AND (VerifyExpires IS NULL OR VerifyExpires > NOW())
         LIMIT 1'
    );
    $stmt->execute([$token]);
    $customer = $stmt->fetch();

    if (!$customer) return false;

    $upd = $pdo->prepare(
        'UPDATE customers
         SET EmailVerified = 1, VerifyToken = NULL, VerifyExpires = NULL
         WHERE CustomerID = ?'
    );
    $upd->execute([$customer['CustomerID']]);

    return true;
}


/* =========================================================
   PASSWORD RESET
   ========================================================= */

function customer_create_reset_token(string $email): ?array
{
    $email = strtolower(trim($email));
    $pdo = db();

    $stmt = $pdo->prepare('SELECT CustomerID, FirstName FROM customers WHERE Email = ? LIMIT 1');
    $stmt->execute([$email]);
    $customer = $stmt->fetch();

    if (!$customer) return null;

    $token   = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', time() + 60 * 60);

    $upd = $pdo->prepare(
        'UPDATE customers SET ResetToken = ?, ResetExpires = ? WHERE CustomerID = ?'
    );
    $upd->execute([$token, $expires, $customer['CustomerID']]);

    return [
        'id'    => (int)$customer['CustomerID'],
        'name'  => $customer['FirstName'],
        'email' => $email,
        'token' => $token,
    ];
}


function customer_reset_password(string $token, string $newPassword): bool
{
    if (strlen($newPassword) < 8) return false;

    $pdo = db();
    $stmt = $pdo->prepare(
        'SELECT CustomerID FROM customers
         WHERE ResetToken = ?
           AND (ResetExpires IS NULL OR ResetExpires > NOW())
         LIMIT 1'
    );
    $stmt->execute([$token]);
    $customer = $stmt->fetch();

    if (!$customer) return false;

    $hash = password_hash($newPassword, PASSWORD_DEFAULT);

    $upd = $pdo->prepare(
        'UPDATE customers
         SET Password = ?,
             ResetToken = NULL,
             ResetExpires = NULL,
             RememberToken = NULL,
             RememberExpires = NULL
         WHERE CustomerID = ?'
    );
    $upd->execute([$hash, $customer['CustomerID']]);

    return true;
}


/* =========================================================
   EMAILS
   ========================================================= */

function customer_send_verification_email(array $customer): bool
{
    try {
        $mail = fete_mailer();
        $mail->addAddress($customer['email'], $customer['name']);

        $link = 'http://localhost/fetecation/verify.php?token=' . urlencode($customer['token']);

        $mail->Subject = 'Welcome to FeteCation — confirm your email';

        $mail->Body =
              "Hi {$customer['name']},\n\n"
            . "Thanks for creating an account with FeteCation!\n\n"
            . "Confirm your email address by visiting this link:\n\n"
            . $link . "\n\n"
            . "This link is valid for 48 hours.\n\n"
            . "Once confirmed, you can sign in and view your bookings anytime.\n\n"
            . "See you soon,\n"
            . "The FeteCation Team\n";

        $mail->send();
        return true;

    } catch (Throwable $e) {
        error_log('Verification email failed: ' . $e->getMessage());
        return false;
    }
}


function customer_send_reset_email(array $customer): bool
{
    try {
        $mail = fete_mailer();
        $mail->addAddress($customer['email'], $customer['name']);

        $link = 'http://localhost/fetecation/reset-password.php?token=' . urlencode($customer['token']);

        $mail->Subject = 'Reset your FeteCation password';

        $mail->Body =
              "Hi {$customer['name']},\n\n"
            . "We received a request to reset your FeteCation password.\n\n"
            . "Set a new password by visiting this link:\n\n"
            . $link . "\n\n"
            . "This link expires in 1 hour.\n\n"
            . "If you didn't request this, you can safely ignore this email.\n\n"
            . "— FeteCation\n";

        $mail->send();
        return true;

    } catch (Throwable $e) {
        error_log('Reset email failed: ' . $e->getMessage());
        return false;
    }
}


/* =========================================================
   PROFILE UPDATES
   ========================================================= */

function customer_update_profile(int $customerId, string $first, string $last, string $phone): array
{
    $first = trim($first);
    $last  = trim($last);
    $phone = trim($phone);

    if ($first === '' || $last === '') {
        return ['ok' => false, 'error' => 'First and last name are required.'];
    }

    try {
        $stmt = db()->prepare(
            'UPDATE customers
             SET FirstName = ?, LastName = ?, Phone = ?
             WHERE CustomerID = ? AND DeletedAt IS NULL'
        );
        $stmt->execute([$first, $last, $phone, $customerId]);

        $_SESSION['customer_name'] = $first;

        return ['ok' => true];

    } catch (Throwable $e) {
        error_log('customer_update_profile failed: ' . $e->getMessage());
        return ['ok' => false, 'error' => 'Could not save changes.'];
    }
}


function customer_update_email(int $customerId, string $newEmail): array
{
    $newEmail = strtolower(trim($newEmail));

    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        return ['ok' => false, 'error' => 'Please enter a valid email address.'];
    }

    $pdo = db();

    $stmt = $pdo->prepare('SELECT CustomerID FROM customers WHERE Email = ? AND CustomerID != ? LIMIT 1');
    $stmt->execute([$newEmail, $customerId]);
    if ($stmt->fetch()) {
        return ['ok' => false, 'error' => 'That email is already in use.'];
    }

    $stmt = $pdo->prepare('SELECT Email, FirstName, LastName FROM customers WHERE CustomerID = ? LIMIT 1');
    $stmt->execute([$customerId]);
    $current = $stmt->fetch();

    if (!$current) {
        return ['ok' => false, 'error' => 'Account not found.'];
    }

    if (strtolower($current['Email']) === $newEmail) {
        return ['ok' => true, 'changed' => false];
    }

    $token  = bin2hex(random_bytes(32));
    $expiry = date('Y-m-d H:i:s', time() + 60 * 60 * 24 * 2);

    $stmt = $pdo->prepare(
        'UPDATE customers
         SET Email = ?, EmailVerified = 0, VerifyToken = ?, VerifyExpires = ?
         WHERE CustomerID = ?'
    );
    $stmt->execute([$newEmail, $token, $expiry, $customerId]);

    customer_send_verification_email([
        'id'    => $customerId,
        'name'  => $current['FirstName'] . ' ' . $current['LastName'],
        'email' => $newEmail,
        'token' => $token,
    ]);

    return ['ok' => true, 'changed' => true];
}


function customer_change_password(int $customerId, string $current, string $new): array
{
    if (strlen($new) < 8) {
        return ['ok' => false, 'error' => 'New password must be at least 8 characters.'];
    }

    $pdo = db();
    $stmt = $pdo->prepare('SELECT Password FROM customers WHERE CustomerID = ? LIMIT 1');
    $stmt->execute([$customerId]);
    $row = $stmt->fetch();

    if (!$row || !password_verify($current, $row['Password'])) {
        usleep(400000);
        return ['ok' => false, 'error' => 'Current password is incorrect.'];
    }

    $hash = password_hash($new, PASSWORD_DEFAULT);

    $upd = $pdo->prepare(
        'UPDATE customers
         SET Password = ?,
             RememberToken = NULL,
             RememberExpires = NULL
         WHERE CustomerID = ?'
    );
    $upd->execute([$hash, $customerId]);

    return ['ok' => true];
}


function customer_delete_account(int $customerId, string $password): array
{
    $pdo = db();
    $stmt = $pdo->prepare('SELECT Password FROM customers WHERE CustomerID = ? LIMIT 1');
    $stmt->execute([$customerId]);
    $row = $stmt->fetch();

    if (!$row || !password_verify($password, $row['Password'])) {
        usleep(400000);
        return ['ok' => false, 'error' => 'Password is incorrect.'];
    }

    $upd = $pdo->prepare(
        'UPDATE customers
         SET DeletedAt = NOW(),
             EmailVerified = 0,
             VerifyToken = NULL,
             ResetToken = NULL,
             RememberToken = NULL,
             RememberExpires = NULL
         WHERE CustomerID = ?'
    );
    $upd->execute([$customerId]);

    return ['ok' => true];
}


/* =========================================================
   REMEMBER ME — persistent login tokens
   ========================================================= */

const REMEMBER_COOKIE_NAME = 'fetecation_remember';
const REMEMBER_DAYS        = 30;

/**
 * Issue a "remember me" cookie for the given customer.
 */
function customer_issue_remember_token(int $customerId): void
{
    if (headers_sent()) return;

    $token = bin2hex(random_bytes(32));
    $hash  = hash('sha256', $token);
    $expires = date('Y-m-d H:i:s', time() + (REMEMBER_DAYS * 86400));

    try {
        $stmt = db()->prepare(
            'UPDATE customers
             SET RememberToken = ?, RememberExpires = ?
             WHERE CustomerID = ?'
        );
        $stmt->execute([$hash, $expires, $customerId]);
    } catch (Throwable $e) {
        error_log('remember token issue failed: ' . $e->getMessage());
        return;
    }

    $value = $customerId . ':' . $token;

    setcookie(
        REMEMBER_COOKIE_NAME,
        $value,
        [
            'expires'  => time() + (REMEMBER_DAYS * 86400),
            'path'     => '/',
            'secure'   => !empty($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax',
        ]
    );
}


/**
 * Clear the remember-me cookie and DB token.
 */
function customer_clear_remember_token(?int $customerId = null): void
{
    if (!headers_sent()) {
        setcookie(
            REMEMBER_COOKIE_NAME,
            '',
            [
                'expires'  => time() - 3600,
                'path'     => '/',
                'secure'   => !empty($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax',
            ]
        );
    }

    if ($customerId !== null && $customerId > 0) {
        try {
            $stmt = db()->prepare(
                'UPDATE customers
                 SET RememberToken = NULL, RememberExpires = NULL
                 WHERE CustomerID = ?'
            );
            $stmt->execute([$customerId]);
        } catch (Throwable $e) {
            error_log('remember token clear failed: ' . $e->getMessage());
        }
    }
}


/**
 * Attempt to auto-login using the remember-me cookie.
 */
function customer_try_remember_login(): bool
{
    if (customer_is_logged_in()) return true;

    if (empty($_COOKIE[REMEMBER_COOKIE_NAME])) return false;

    $raw = (string)$_COOKIE[REMEMBER_COOKIE_NAME];

    $parts = explode(':', $raw, 2);
    if (count($parts) !== 2) {
        customer_clear_remember_token();
        return false;
    }

    $customerId = (int)$parts[0];
    $token      = $parts[1];

    if ($customerId < 1 || $token === '') {
        customer_clear_remember_token();
        return false;
    }

    $hash = hash('sha256', $token);

    try {
        $stmt = db()->prepare(
            'SELECT * FROM customers
             WHERE CustomerID = ?
               AND RememberToken = ?
               AND RememberExpires > NOW()
               AND DeletedAt IS NULL
             LIMIT 1'
        );
        $stmt->execute([$customerId, $hash]);
        $customer = $stmt->fetch();

    } catch (Throwable $e) {
        error_log('remember lookup failed: ' . $e->getMessage());
        return false;
    }

    if (!$customer) {
        customer_clear_remember_token($customerId);
        return false;
    }

    customer_session_start();
    if (!headers_sent()) {
        session_regenerate_id(true);
    }

    $_SESSION['customer_id']   = (int)$customer['CustomerID'];
    $_SESSION['customer_name'] = $customer['FirstName'];

    customer_issue_remember_token((int)$customer['CustomerID']);

    return true;
}


/* =========================================================
   AUTO-LOGIN — runs whenever this file is included
   ========================================================= */

customer_try_remember_login();