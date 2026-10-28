<?php
/**
 * Tour & Party data — now powered by MySQL.
 * Falls back to static arrays only if the DB fails.
 */

require_once __DIR__ . '/includes/database.php';

$TOURS = [];
$PARTIES = [];

try {
    $stmt = $pdo->query('SELECT * FROM tours ORDER BY id ASC');
    $rows = $stmt->fetchAll();

    foreach ($rows as $row) {
        // Rebuild the array structure exactly as the site expects it
        $item = [
            'name'             => $row['name'],
            'tag'              => $row['tag'],
            'tagline'          => $row['tagline'],
            'emoji'            => $row['emoji'],
            'image'            => $row['image'],
            'duration'         => $row['duration'],
            'group'            => $row['group_size'],
            'price'            => $row['price_display'],
            'price_per_person' => $row['price_amount'],
            'price_type'       => $row['price_type'],
            'pickup'           => $row['pickup'],
            'multi_stop_price' => $row['multi_stop_price'],
            'about'            => json_decode($row['about'], true) ?: [],
            'included'         => json_decode($row['included'], true) ?: [],
            'bring'            => json_decode($row['bring'], true) ?: [],
            'itinerary'        => json_decode($row['itinerary'], true) ?: [],
        ];

        // Route to the correct array based on type
        if ($row['type'] === 'party') {
            $PARTIES[$row['slug']] = $item;
        } else {
            $TOURS[$row['slug']] = $item;
        }
    }

} catch (Throwable $e) {
    error_log('Failed to load tours/parties from DB: ' . $e->getMessage());
}

// =========================================================
// HELPER FUNCTIONS
// =========================================================

function fete_tour($slug) {
    global $TOURS, $PARTIES;
    if (isset($TOURS[$slug])) return $TOURS[$slug];
    if (isset($PARTIES[$slug])) return $PARTIES[$slug];
    return null;
}

function fete_all_tours() {
    global $TOURS;
    return $TOURS;
}

function fete_all_parties() {
    global $PARTIES;
    return $PARTIES;
}

function fete_tour_payment_options(string $slug): array
{
    $tour = fete_tour($slug);
    
    // If it's a party, default to deposit/full only
    if ($tour && ($tour['tag'] ?? '') === 'PARTY PACKAGE') {
        return ['full', 'deposit'];
    }

    // Standard tours
    $paidOnly = ['island-highlights'];
    if (in_array($slug, $paidOnly, true)) {
        return ['full', 'deposit'];
    }

    return ['full', 'deposit', 'on-day'];
}

function fete_tour_price(string $slug): array
{
    if ($slug === 'taxi') {
        return ['type' => 'per_person', 'amount' => 40.00];
    }

    $tour = fete_tour($slug);

    if (!$tour) {
        return ['type' => 'custom', 'amount' => null];
    }

    return [
        'type'   => $tour['price_type'] ?? 'per_person',
        'amount' => $tour['price_per_person'] ?? null,
    ];
}

function fete_calculate_booking_totals(string $slug, int $guests): array
{
    $price = fete_tour_price($slug);

    if ($price['type'] === 'custom' || $price['amount'] === null) {
        return [
            'total'        => null,
            'deposit'      => null,
            'due_on_day'   => null,
            'per_person'   => null,
            'guests'       => max(1, $guests),
            'is_custom'    => true,
        ];
    }

    $guests = max(1, $guests);
    
    if ($price['type'] === 'per_group') {
        $total = round($price['amount'], 2);
        $per_person = null;
    } else {
        $total = round($price['amount'] * $guests, 2);
        $per_person = $price['amount'];
    }

    $deposit = round($total * (DEPOSIT_PERCENT / 100), 2);

    return [
        'total'      => $total,
        'deposit'    => $deposit,
        'due_on_day' => round($total - $deposit, 2),
        'per_person' => $per_person,
        'guests'     => $guests,
        'is_custom'  => false,
    ];
}

function fete_generate_reference(): string
{
    return strtoupper(bin2hex(random_bytes(3)));
}

function fete_booking_totals_with_override(string $slug, int $guests, ?float $customPrice = null): array
{
    $guests = max(1, $guests);

    if ($customPrice !== null && $customPrice > 0) {
        $total = round($customPrice, 2);
        $deposit = round($total * (DEPOSIT_PERCENT / 100), 2);

        return [
            'total'      => $total,
            'deposit'    => $deposit,
            'due_on_day' => round($total - $deposit, 2),
            'per_person' => null,
            'guests'     => $guests,
            'is_custom'  => false,
            'is_override'=> true,
        ];
    }

    $base = fete_calculate_booking_totals($slug, $guests);
    $base['is_override'] = false;
    return $base;
}