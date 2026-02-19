<?php
// api/ai_advice.php

// Define products to replicate the logic in JS/TS
$PRODUCTS = [
  [
    'id' => 'p1',
    'name' => 'Nutra_leaf Ashwagandha Gold',
    'category' => 'Stress Relief',
    'benefits' => ['Reduces Stress', 'Improves Focus', 'Boosts Immunity']
  ],
  [
    'id' => 'p2',
    'name' => 'Nutra_leaf Moringa Power',
    'category' => 'Superfoods',
    'benefits' => ['Full Spectrum Nutrition', 'Energy Boost', 'Detoxifying']
  ]
];

header('Content-Type: application/json');

// Get the raw JSON body
$json_data = file_get_contents('php://input');
$request = json_decode($json_data, true);

$query = strtolower($request['query'] ?? '');
$advice = 'I am currently unable to provide product advice. Please check back later.';

if (strpos($query, 'stress') !== false || strpos($query, 'calm') !== false) {
    $advice = 'The **Nutra_leaf Ashwagandha Gold** is specifically formulated for **Stress Relief** and improved focus. It contains ultra-pure KSM-66 Ashwagandha for maximum potency.';
} elseif (strpos($query, 'energy') !== false || strpos($query, 'vitality') !== false || strpos($query, 'superfood') !== false) {
    $advice = 'The **Nutra_leaf Moringa Power** is your go-to for **Energy Boost** and full spectrum nutrition. It is a cold-pressed organic superfood supplement.';
} else {
    $advice = 'Please ask about stress, energy, or general wellness to get a tailored product recommendation.';
}

// Simulate a slight delay for realism
usleep(500000); 

echo json_encode(['advice' => $advice]);
?>