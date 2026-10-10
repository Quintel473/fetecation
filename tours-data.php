<?php
/**
 * Tour data — single source of truth.
 * Each tour is keyed by its slug (used in the URL).
 */

$TOURS = [

    'island-highlights' => [
        'name'             => 'Tipsy Tourist Tour',
        'tag'              => 'POPULAR',
        'tagline'          => 'See the best of the island in one unforgettable day.',
        'emoji'            => '🌅',
        'image'            => '/fetecation/images/tours/island-highlights-1.jpg',
        'duration'         => '4–6 hours',
        'group'            => 'Up to 6 people',
        'price'            => 'From $120 / person',   // display string
        'price_per_person' => 120.00,                  // calculation
        'price_type'       => 'per_person',            // per_person | custom
        'pickup'           => 'Hotel or cruise port',

        'about' => [
            'Discover the island\'s most beautiful spots on a relaxed, private tour designed around you.',
            'Visit scenic viewpoints, golden beaches and cultural landmarks while your local guide shares stories about the island\'s history and way of life.',
            'Perfect for first-time visitors who want to see it all without the stress of planning.',
        ],

        'included' => [
            'Air-conditioned private vehicle',
            'Local English-speaking guide',
            'Bottled water throughout the day',
            'Hotel or cruise port pickup & drop-off',
            'Flexible stops for photos',
        ],

        'bring' => [
            'Comfortable walking shoes',
            'Sunscreen & sunglasses',
            'Swimsuit & towel',
            'Camera or phone',
            'Cash for souvenirs & snacks',
        ],

        'itinerary' => [
            ['time' => '9:00 AM',  'title' => 'Pickup from your hotel',       'text' => 'Your guide meets you at your accommodation or cruise port.'],
            ['time' => '9:30 AM',  'title' => 'Scenic viewpoint stop',        'text' => 'Panoramic photo stop overlooking the coastline.'],
            ['time' => '11:00 AM', 'title' => 'Beach time',                   'text' => 'Relax on a pristine beach with time to swim.'],
            ['time' => '1:00 PM',  'title' => 'Local lunch (optional)',       'text' => 'Stop at a favourite local spot for authentic cuisine.'],
            ['time' => '3:00 PM',  'title' => 'Cultural landmark visit',      'text' => 'Learn about the island\'s history at a key landmark.'],
            ['time' => '5:00 PM',  'title' => 'Return to your accommodation', 'text' => 'Drop-off at your hotel or cruise port.'],
        ],
    ],

    'nature-adventure' => [
        'name'             => 'Nature & Adventure',
        'tag'              => 'EXPERIENCE',
        'tagline'          => 'Get off the beaten path and into the wild.',
        'emoji'            => '🌴',
        'image'            => '/fetecation/images/tours/nature-adventure-1.jpg',
        'duration'         => '5–7 hours',
        'group'            => 'Up to 6 people',
        'price'            => 'From $150 / person',
        'price_per_person' => 150.00,
        'price_type'       => 'per_person',
        'pickup'           => 'Hotel or cruise port',

        'about' => [
            'For those who want more than a beach day — this tour takes you deep into the island\'s natural beauty.',
            'Hike to hidden waterfalls, swim in natural pools and discover local flora and fauna with a guide who knows every trail.',
            'A perfect blend of adventure, scenery and local culture.',
        ],

        'included' => [
            'Air-conditioned private vehicle',
            'Local adventure guide',
            'Bottled water & snacks',
            'Entrance fees to natural sites',
            'Hotel or cruise port pickup & drop-off',
        ],

        'bring' => [
            'Sturdy walking or hiking shoes',
            'Swimsuit & towel',
            'Sunscreen & insect repellent',
            'Change of clothes',
            'Camera or phone',
        ],

        'itinerary' => [
            ['time' => '8:30 AM',  'title' => 'Pickup from your hotel',       'text' => 'Meet your guide and head inland.'],
            ['time' => '9:30 AM',  'title' => 'Rainforest trail hike',        'text' => 'Guided hike through lush tropical vegetation.'],
            ['time' => '11:00 AM', 'title' => 'Hidden waterfall swim',        'text' => 'Cool off in a natural freshwater pool.'],
            ['time' => '12:30 PM', 'title' => 'Scenic picnic lunch',          'text' => 'Enjoy a packed lunch overlooking the valley.'],
            ['time' => '2:30 PM',  'title' => 'Local farm or rum stop',       'text' => 'Taste local produce or rum at a family-run spot.'],
            ['time' => '5:00 PM',  'title' => 'Return to your accommodation', 'text' => 'Drop-off at your hotel or cruise port.'],
        ],
    ],

    'custom-island' => [
        'name'             => 'Custom Island Experience',
        'tag'              => 'CUSTOM',
        'tagline'          => 'Your island, your way — built around what you love.',
        'emoji'            => '🌅',
        'image'            => '/fetecation/images/tours/custom-island-1.jpg',
        'duration'         => 'Custom schedule',
        'group'            => 'Up to 12 people',
        'price'            => 'Custom quote',
        'price_per_person' => null,                    // custom → no fixed price
        'price_type'       => 'custom',
        'pickup'           => 'Anywhere on the island',

        'about' => [
            'Want something completely different? Tell us what you\'re dreaming of and we\'ll build the perfect day around it — no fixed itinerary, no rushing, no compromises.',
            'Wedding parties, family reunions, photography trips, food tours, sunset cruises, corporate retreats — if it\'s possible on the island, we\'ll make it happen.',
            'Just share your ideas, group size and preferred date and we\'ll send you a tailored itinerary and price within 24 hours.',
        ],

        'included' => [
            'A fully personalised itinerary built around your group',
            'Dedicated local guide for the entire experience',
            'Air-conditioned private vehicle (sedan, SUV or van)',
            'Bottled water and refreshments throughout the day',
            'Pickup and drop-off anywhere on the island',
            'Flexible timing — you set the pace',
            'Coordination with restaurants, venues or activity providers',
        ],

        'bring' => [
            'Your ideas, wishlist and must-see spots',
            'Group size and any special requests',
            'Comfortable shoes for the day',
            'Sunscreen and a hat',
            'Camera or phone (you\'ll want photos)',
        ],

        'how_it_works' => [
            [
                'step'  => '01',
                'title' => 'Tell us your vision',
                'text'  => 'Share your ideas, group size, date and any must-do experiences. Even a rough wishlist is enough to get started.',
            ],
            [
                'step'  => '02',
                'title' => 'We craft your itinerary',
                'text'  => 'We design a personalised plan with timing, stops, activities and pricing — usually within 24 hours.',
            ],
            [
                'step'  => '03',
                'title' => 'You refine the details',
                'text'  => 'Tweak anything you like. Add a stop, remove one, adjust timings — we\'ll keep revising until it\'s perfect.',
            ],
            [
                'step'  => '04',
                'title' => 'We make it happen',
                'text'  => 'On the day, your guide picks you up and everything runs smoothly. All you have to do is enjoy it.',
            ],
        ],

        'ideas' => [
            ['icon' => '💍', 'title' => 'Wedding & celebrations'],
            ['icon' => '📸', 'title' => 'Photography tours'],
            ['icon' => '🍽️', 'title' => 'Food & rum experiences'],
            ['icon' => '👨‍👩‍👧‍👦', 'title' => 'Family reunions'],
            ['icon' => '🌅', 'title' => 'Sunset & scenic drives'],
            ['icon' => '🎉', 'title' => 'Bachelorette & group trips'],
            ['icon' => '🏝️', 'title' => 'Hidden beaches & local spots'],
            ['icon' => '🎯', 'title' => 'Anything you can dream up'],
        ],
    ],
];

function fete_tour($slug) {
    global $TOURS;
    return isset($TOURS[$slug]) ? $TOURS[$slug] : null;
}

function fete_all_tours() {
    global $TOURS;
    return $TOURS;
}

/**
 * Return the payment options allowed for a given tour slug.
 */
function fete_tour_payment_options(string $slug): array
{
    /* Tours that require payment upfront (no on-day) */
    $paidOnly = ['island-highlights'];

    if (in_array($slug, $paidOnly, true)) {
        return ['full', 'deposit'];
    }

    return ['full', 'deposit', 'on-day'];
}

/**
 * Look up a tour's price data by slug.
 * Returns ['type' => 'per_person'|'per_trip'|'custom', 'amount' => float|null]
 */
function fete_tour_price(string $slug): array
{
    /* Taxi is a fixed-price service, not in $TOURS */
    if ($slug === 'taxi') {
        return [
            'type'   => 'per_person',
            'amount' => 40.00,   // ← change to your real taxi price
        ];
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

/**
 * Calculate total + deposit + due-on-day for a booking.
 * Returns an array of floats, or nulls if the price is custom.
 */
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
    $total  = round($price['amount'] * $guests, 2);
    $deposit = round($total * (DEPOSIT_PERCENT / 100), 2);

    return [
        'total'      => $total,
        'deposit'    => $deposit,
        'due_on_day' => round($total - $deposit, 2),
        'per_person' => $price['amount'],
        'guests'     => $guests,
        'is_custom'  => false,
    ];
}

/**
 * Generate a unique booking reference.
 * Format: 6-char uppercase hex (e.g. A3F9C1)
 */
function fete_generate_reference(): string
{
    return strtoupper(bin2hex(random_bytes(3)));
}

/**
 * Calculate booking totals with an optional custom price override.
 * If $customPrice is set (> 0), it overrides the per-person total.
 */
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