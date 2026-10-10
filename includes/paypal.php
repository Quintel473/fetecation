<?php
/**
 * PayPal integration helper.
 *
 * ─────────────────────────────────────────────────────────────
 *  ⚠️  PLACEHOLDERS — REPLACE BEFORE GOING LIVE
 * ─────────────────────────────────────────────────────────────
 *  1. Go to https://developer.paypal.com/dashboard/applications
 *  2. Create an app (Live mode)
 *  3. Paste your Client ID and Secret below
 *  4. Change PAYPAL_MODE from 'sandbox' to 'live'
 * ─────────────────────────────────────────────────────────────
 */

/* ============ CONFIG — REPLACE THESE ============ */

const PAYPAL_MODE          = 'sandbox';                              // 'sandbox' or 'live'
const PAYPAL_CLIENT_ID     = 'PAYPAL_CLIENT_ID_PLACEHOLDER';         // ← replace
const PAYPAL_SECRET        = 'PAYPAL_SECRET_PLACEHOLDER';            // ← replace
const PAYPAL_BUSINESS_EMAIL= 'quintelcharles@gmail.com';             // ← your PayPal business email
const PAYPAL_CURRENCY      = 'USD';                                  // USD or XCD

const PAYPAL_RETURN_URL    = 'http://localhost/fetecation/payment-success.php';
const PAYPAL_CANCEL_URL    = 'http://localhost/fetecation/payment-cancelled.php';
const PAYPAL_WEBHOOK_URL   = 'http://localhost/fetecation/paypal-webhook.php';

const DEPOSIT_PERCENT      = 20;   // 20% deposit option

/* ============ API ENDPOINTS ============ */

function paypal_api_base(): string
{
    return PAYPAL_MODE === 'live'
        ? 'https://api-m.paypal.com'
        : 'https://api-m.sandbox.paypal.com';
}

function paypal_get_access_token(): ?string
{
    static $token = null;
    if ($token !== null) return $token;

    if (PAYPAL_CLIENT_ID === 'PAYPAL_CLIENT_ID_PLACEHOLDER') {
        error_log('PayPal: placeholder credentials — payment flow disabled.');
        return null;
    }

    $ch = curl_init(paypal_api_base() . '/v1/oauth2/token');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_USERPWD        => PAYPAL_CLIENT_ID . ':' . PAYPAL_SECRET,
        CURLOPT_POSTFIELDS     => 'grant_type=client_credentials',
        CURLOPT_HTTPHEADER     => ['Accept: application/json'],
    ]);

    $response = curl_exec($ch);
    $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status !== 200 || !$response) return null;

    $data = json_decode($response, true);
    if (empty($data['access_token'])) return null;

    return $token = $data['access_token'];
}


/**
 * Create a PayPal order and return the approval URL.
 * Returns ['ok' => bool, 'url' => string, 'order_id' => string, 'error' => string]
 */
function paypal_create_order(float $amount, string $reference, string $description): array
{
    if (PAYPAL_CLIENT_ID === 'PAYPAL_CLIENT_ID_PLACEHOLDER') {

        /* PLACEHOLDER MODE — returns a fake URL for testing the UI */
        return [
            'ok'       => true,
            'url'      => 'https://www.sandbox.paypal.com/checkoutnow?token=PLACEHOLDER_' . urlencode($reference),
            'order_id' => 'PLACEHOLDER_ORDER_' . $reference,
            'placeholder' => true,
        ];
    }

    $token = paypal_get_access_token();
    if (!$token) {
        return ['ok' => false, 'error' => 'Could not authenticate with PayPal.'];
    }

    $payload = [
        'intent' => 'CAPTURE',
        'purchase_units' => [[
            'reference_id' => $reference,
            'description'  => $description,
            'amount' => [
                'currency_code' => PAYPAL_CURRENCY,
                'value'         => number_format($amount, 2, '.', ''),
            ],
        ]],
        'application_context' => [
            'brand_name'  => 'FeteCation Taxi & Tours',
            'user_action' => 'PAY_NOW',
            'return_url'  => PAYPAL_RETURN_URL . '?ref=' . urlencode($reference),
            'cancel_url'  => PAYPAL_CANCEL_URL . '?ref=' . urlencode($reference),
        ],
    ];

    $ch = curl_init(paypal_api_base() . '/v2/checkout/orders');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token,
        ],
    ]);

    $response = curl_exec($ch);
    $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $data = json_decode($response, true);

    if ($status !== 201 || empty($data['links'])) {
        error_log('PayPal create order failed: ' . $response);
        return ['ok' => false, 'error' => 'PayPal rejected the order.'];
    }

    $approvalUrl = '';
    foreach ($data['links'] as $link) {
        if (($link['rel'] ?? '') === 'approve') {
            $approvalUrl = $link['href'];
            break;
        }
    }

    return [
        'ok'       => true,
        'url'      => $approvalUrl,
        'order_id' => $data['id'] ?? '',
    ];
}


/**
 * Capture a PayPal order after the customer approves it.
 * Returns ['ok' => bool, 'error' => string]
 */
function paypal_capture_order(string $orderId): array
{
    if (PAYPAL_CLIENT_ID === 'PAYPAL_CLIENT_ID_PLACEHOLDER') {
        return ['ok' => true, 'placeholder' => true];
    }

    $token = paypal_get_access_token();
    if (!$token) return ['ok' => false, 'error' => 'Auth failed'];

    $ch = curl_init(paypal_api_base() . '/v2/checkout/orders/' . urlencode($orderId) . '/capture');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => '',
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token,
        ],
    ]);

    $response = curl_exec($ch);
    $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status !== 201) {
        error_log('PayPal capture failed: ' . $response);
        return ['ok' => false, 'error' => 'Capture failed.'];
    }

    return ['ok' => true];
}


/**
 * Calculate the amount to charge based on payment option.
 */
function paypal_calculate_amount(string $tourSlug, int $guests, string $option): float
{
    /* Base price per person — replace with your real prices */
    $basePrice = 120.00;   // default per-person price

    $total = $basePrice * max(1, $guests);

    if ($option === 'deposit') {
        return round($total * (DEPOSIT_PERCENT / 100), 2);
    }

    return round($total, 2);
}


/**
 * Log a payment record to disk for record-keeping.
 */
function paypal_log_payment(array $data): void
{
    $dir = __DIR__ . '/../data/payments';
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }

    $filename = $dir . '/' . date('Y-m-d_His') . '_' . ($data['reference'] ?? 'unknown') . '.json';
    @file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
}