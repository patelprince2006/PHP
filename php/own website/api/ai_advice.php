<?php
// ai_advice.php - simple rule-based product advice (fallback for geminiService)
header('Content-Type: application/json');
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
$q = isset($data['query']) ? trim($data['query']) : '';
if(!$q){ http_response_code(400); echo json_encode(['error'=>'missing query']); exit; }

$q_l = strtolower($q);
$advice = '';

// Basic keyword matching to recommend product
if(strpos($q_l, 'sleep') !== false || strpos($q_l, 'stress') !== false || strpos($q_l, 'anxiety') !== false){
  $advice = "For stress and sleep support, Nutra_leaf Ashwagandha Gold (KSM-66) is a strong choice — it helps promote relaxation and cognitive resilience. Consider taking it as part of an evening routine and consult your healthcare provider if you take medications.";
} elseif(strpos($q_l, 'energy') !== false || strpos($q_l, 'nutrition') !== false || strpos($q_l, 'immunity') !== false){
  $advice = "Nutra_leaf Moringa Power is nutrient-dense and can support daily energy and overall nutrition. It's best used as a daily supplement mixed into smoothies or water for consistent benefits.";
} else {
  $advice = "Both products have distinct uses: Ashwagandha is targeted toward stress, sleep and focus; Moringa is a broad-spectrum superfood for nutrition and energy. Tell me if your main goal is sleep, stress relief, or energy, and I can recommend one specifically.";
}

echo json_encode(['query'=>$q,'advice'=>$advice]);
exit;
