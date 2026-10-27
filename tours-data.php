<?php
/**
 * Tour & Party data — single source of truth.
 */

// =========================================================
// REGULAR TOURS
// =========================================================
$TOURS = [

    'island-highlights' => [
        'name'             => 'Tipsy Tourist Tour',
        'tag'              => 'POPULAR',
        'tagline'          => 'See the best of the island in one unforgettable day.',
        'emoji'            => '🌅',
        'image'            => '/fetecation/images/tours/island-highlights-1.jpg',
        'duration'         => '4–6 hours',
        'group'            => 'Up to 6 people',
        'price'            => 'From $120 / person',
        'price_per_person' => 120.00,
        'price_type'       => 'per_person',
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
        'price_per_person' => null,
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

// =========================================================
// PARTY PACKAGES
// =========================================================
$PARTIES = [

    'friday-night-lime' => [
        'name'             => 'Friday Night Lime Party Bus',
        'tag'              => 'PARTY PACKAGE',
        'tagline'          => 'Club hopping, drinks, and vibes all night long.',
        'emoji'            => '🍾',
        'image'            => '/fetecation/images/parties/friday-lime.jpg', 
        'duration'         => '5 hours',
        'group'            => 'Up to 15 people',
        'price'            => '$350 / group',
        'price_per_person' => 350.00, 
        'price_type'       => 'per_group', 
        'pickup'           => 'Hotel or cruise port',
        'multi_stop_price' => 25.00, 

        'about' => [
            'Let us handle the driving while you handle the party!',
            'We pick up your group, take you to the hottest local spots, keep the party going in the vehicle, and get everyone home safely.',
        ],

        'included' => [
            'Private party vehicle',
            'Dedicated driver for the night',
            'Custom multi-stop itinerary',
            'Ice and cooler provided',
        ],
    ],

    'saturday-beach-bash' => [
        'name'             => 'Saturday Sunset Beach Bash',
        'tag'              => 'PARTY PACKAGE',
        'tagline'          => 'Golden hour vibes, live music, and bonfires.',
        'emoji'            => '🔥',
        'image'            => '/fetecation/images/parties/beach-bash.jpg',
        'duration'         => '4 hours',
        'group'            => 'Up to 20 people',
        'price'            => '$400 / group',
        'price_per_person' => 400.00,
        'price_type'       => 'per_group',
        'pickup'           => 'Hotel or cruise port',
        'multi_stop_price' => 20.00,

        'about' => [
            'Experience the best sunset views on the island with your crew.',
            'We provide the transport, the cooler, and the driver. You bring the vibes.',
        ],

        'included' => [
            'Private party vehicle',
            'Driver on standby',
            'Cooler with ice',
            'Beach drop-off and pickup',
        ],
    ],

    'ladies-night-out' => [
        'name'             => 'Ladies Night Out',
        'tag'              => 'PARTY PACKAGE',
        'tagline'          => 'VIP treatment, cocktails, and the best clubs.',
        'emoji'            => '🍸',
        'image'            => '/fetecation/images/parties/ladies-night.jpg',
        'duration'         => '5 hours',
        'group'            => 'Up to 12 people',
        'price'            => '$300 / group',
        'price_per_person' => 300.00,
        'price_type'       => 'per_group',
        'pickup'           => 'Hotel or cruise port',
        'multi_stop_price' => 15.00,

        'about' => [
            'Grab your girls and let us take you on a VIP tour of the island nightlife.',
            'Complimentary bubbly on arrival to get the night started right.',
        ],

        'included' => [
            'Private SUV or van',
            'Professional driver',
            'Complimentary bubbly',
            'Multi-stop club hopping',
        ],
    ],

    'boat-party-transfer' => [
        'name'             => 'Boat Party Shuttle',
        'tag'              => 'PARTY PACKAGE',
        'tagline'          => 'Direct transport to the marina and back.',
        'emoji'            => '🛥️',
        'image'            => '/fetecation/images/parties/boat-party.jpg',
        'duration'         => '6 hours',
        'group'            => 'Up to 25 people',
        'price'            => '$500 / group',
        'price_per_person' => 500.00,
        'price_type'       => 'per_group',
        'pickup'           => 'Hotel or cruise port',
        'multi_stop_price' => 30.00,

        'about' => [
            'Heading to a boat party? Avoid the hassle of parking and taxis.',
            'We handle all the ground transport so you can focus on the sea.',
        ],

        'included' => [
            'Large party bus or van',
            'Round-trip transport',
            'Driver waits for your return',
            'Cooler provided',
        ],
    ],

    'bachelor-party' => [
        'name'             => 'Bachelor Party Blowout',
        'tag'              => 'PARTY PACKAGE',
        'tagline'          => 'The ultimate guys trip around the island.',
        'emoji'            => '🥃',
        'image'            => '/fetecation/images/parties/bachelor.jpg',
        'duration'         => '6 hours',
        'group'            => 'Up to 15 people',
        'price'            => '$450 / group',
        'price_per_person' => 450.00,
        'price_type'       => 'per_group',
        'pickup'           => 'Hotel or cruise port',
        'multi_stop_price' => 25.00,

        'about' => [
            'Celebrate the groom in style. From beach bars to clubs, we know the best spots.',
            'We keep the party safe and moving all night long.',
        ],

        'included' => [
            'Private party vehicle',
            'Local guide/driver',
            'Cooler with ice',
            'Custom itinerary',
        ],
    ],

    'bachelorette-party' => [
        'name'             => 'Bachelorette Party Bash',
        'tag'              => 'PARTY PACKAGE',
        'tagline'          => 'Unforgettable fun for the bride-to-be.',
        'emoji'            => '👑',
        'image'            => '/fetecation/images/parties/bachelorette.jpg',
        'duration'         => '5 hours',
        'group'            => 'Up to 12 people',
        'price'            => '$350 / group',
        'price_per_person' => 350.00,
        'price_type'       => 'per_group',
        'pickup'           => 'Hotel or cruise port',
        'multi_stop_price' => 20.00,

        'about' => [
            'Give the bride the send-off she deserves.',
            'We coordinate with clubs and restaurants to make sure your group gets VIP treatment.',
        ],

        'included' => [
            'Private party vehicle',
            'Dedicated driver',
            'Custom party playlist',
            'Multi-stop transport',
        ],
    ],

    'birthday-party' => [
        'name'             => 'Birthday Party Cruiser',
        'tag'              => 'PARTY PACKAGE',
        'tagline'          => 'Celebrate another year in island style.',
        'emoji'            => '🎂',
        'image'            => '/fetecation/images/parties/birthday.jpg',
        'duration'         => '4 hours',
        'group'            => 'Up to 20 people',
        'price'            => '$300 / group',
        'price_per_person' => 300.00,
        'price_type'       => 'per_group',
        'pickup'           => 'Hotel or cruise port',
        'multi_stop_price' => 15.00,

        'about' => [
            'Whether it is a sweet 16 or a 50th, we make sure the birthday star arrives in style.',
            'Perfect for getting the whole family or friend group to the party safely.',
        ],

        'included' => [
            'Private party vehicle',
            'Driver on standby',
            'Custom pickup and drop-offs',
            'Balloon decoration available',
        ],
    ],

    'carnival-jouvert' => [
        'name'             => 'Carnival J\'ouvert Express',
        'tag'              => 'PARTY PACKAGE',
        'tagline'          => 'Get to the street party before sunrise.',
        'emoji'            => '🎉',
        'image'            => '/fetecation/images/parties/carnival.jpg',
        'duration'         => '8 hours',
        'group'            => 'Up to 20 people',
        'price'            => '$600 / group',
        'price_per_person' => 600.00,
        'price_type'       => 'per_group',
        'pickup'           => 'Hotel or cruise port',
        'multi_stop_price' => 35.00,

        'about' => [
            'Carnival is the biggest party of the year. Beat the traffic and the parking nightmare.',
            'We drop you right at the action and pick you up when you are ready to go home.',
        ],

        'included' => [
            'Party bus transport',
            'Early morning pickup',
            'Cooler provided',
            'Flexible return time',
        ],
    ],

    'karaoke-night-cruise' => [
        'name'             => 'Karaoke Night Cruise',
        'tag'              => 'PARTY PACKAGE',
        'tagline'          => 'Sing your heart out as we cruise the island.',
        'emoji'            => '🎤',
        'image'            => '/fetecation/images/parties/karaoke.jpg',
        'duration'         => '4 hours',
        'group'            => 'Up to 12 people',
        'price'            => '$300 / group',
        'price_per_person' => 300.00,
        'price_type'       => 'per_group',
        'pickup'           => 'Hotel or cruise port',
        'multi_stop_price' => 15.00,

        'about' => [
            'No crowd, no stage fright. Just you, your friends, and your favorite songs.',
            'Our driver provides the mic and the aux cord. You provide the entertainment.',
        ],

        'included' => [
            'Private party vehicle',
            'Bluetooth sound system',
            'Wireless microphones',
            'Cooler with ice',
        ],
    ],

];

// =========================================================
// HELPER FUNCTIONS
// =========================================================

/**
 * Get a specific tour OR party by slug.
 */
function fete_tour($slug) {
    global $TOURS, $PARTIES;
    if (isset($TOURS[$slug])) return $TOURS[$slug];
    if (isset($PARTIES[$slug])) return $PARTIES[$slug];
    return null;
}

/**
 * Get ONLY the regular tours (for Tours page & Booking dropdown).
 */
function fete_all_tours() {
    global $TOURS;
    return $TOURS;
}

/**
 * Get ONLY the party packages (for Parties page).
 */
function fete_all_parties() {
    global $PARTIES;
    return $PARTIES;
}

/**
 * Return the payment options allowed for a given tour/party slug.
 */
function fete_tour_payment_options(string $slug): array
{
    $paidOnly = [
        'island-highlights',
        'friday-night-lime',
        'saturday-beach-bash',
        'ladies-night-out',
        'boat-party-transfer',
        'bachelor-party',
        'bachelorette-party',
        'birthday-party',
        'carnival-jouvert',
        'karaoke-night-cruise'
    ];

    if (in_array($slug, $paidOnly, true)) {
        return ['full', 'deposit'];
    }

    return ['full', 'deposit', 'on-day'];
}

/**
 * Look up a tour's/party's price data by slug.
 */
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

/**
 * Calculate total + deposit + due-on-day for a booking.
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

/**
 * Generate a unique booking reference.
 */
function fete_generate_reference(): string
{
    return strtoupper(bin2hex(random_bytes(3)));
}

/**
 * Calculate booking totals with an optional custom price override.
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

    $base = fete_booking_totals_with_override($slug, $guests, null);
    $base['is_override'] = false;
    return $base;
}