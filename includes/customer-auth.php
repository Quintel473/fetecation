<?php
/**
 * Customer authentication — register, login, verify, reset.
 * Works with the existing includes/database.php ($pdo).
 */

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/mailer.php';


/* =========================================================
   DB ACCESSOR
   Wraps the global $pdo so it's usable inside functions.
   ========================================================= */

function db(): PDO
{
    global $pdo;
    return $pdo;
}


/* =========================================================
   SESSION
   ========================================================= */

function customer_session_start(): void
{
    if (session_status() === PHP_SESSION_NONE) {
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
        $stmt = db()->prepare('SELECT * FROM customers WHERE CustomerID = ? LIMIT 1');
        $stmt->execute([$_SESSION['customer_id']]);
        $row = $stmt->fetch();

        return $customer = $row ?: null;

    } catch (Throwable $e) {
        return null;
    }
}

function customer_logout(): void
{
    customer_session_start();
    unset($_SESSION['customer_id'], $_SESSION['customer_name']);
    session_regenerate_id(true);
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

    /* Email already used? */
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

    /* Send verification email to the new customer */
    customer_send_verification_email([
        'id'    => $id,
        'name'  => $first . ' ' . $last,
        'email' => $email,
        'token' => $verifyToken,
    ]);

    /* Notify the admin (you) about the new signup */
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

    try {
        $upd = $pdo->prepare('UPDATE customers SET LastLoginAt = NOW() WHERE CustomerID = ?');
        $upd->execute([$customer['CustomerID']]);
    } catch (Throwable $e) { /* non-fatal */ }

    customer_session_start();
    session_regenerate_id(true);

    $_SESSION['customer_id']   = (int)$customer['CustomerID'];
    $_SESSION['customer_name'] = $customer['FirstName'];

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
         SET Password = ?, ResetToken = NULL, ResetExpires = NULL
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