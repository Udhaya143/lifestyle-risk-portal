<?php
function calculate_bmi($height_cm, $weight_kg) {
    if ($height_cm <= 0) return 0;
    $h = $height_cm / 100.0;
    return round($weight_kg / ($h*$h), 2);
}

function bmi_category($bmi) {
    if ($bmi<=0) return 'Unknown';
    if ($bmi < 18.5) return 'Underweight';
    if ($bmi < 25) return 'Normal';
    if ($bmi < 30) return 'Overweight';
    return 'Obese';
}

// Simplified rule-based risk assessment (replaces ML)
function get_risk_prediction($data) {
    $score = 0;

    // BMI Risk (40% weight)
    if ($data['bmi'] >= 30) $score += 40; // Obese
    elseif ($data['bmi'] >= 25) $score += 25; // Overweight
    elseif ($data['bmi'] >= 18.5) $score += 5; // Normal
    else $score += 15; // Underweight

    // Blood Pressure Risk (25% weight)
    if (!empty($data['systolic_bp'])) {
        if ($data['systolic_bp'] >= 180) $score += 25;
        elseif ($data['systolic_bp'] >= 140) $score += 20;
        elseif ($data['systolic_bp'] >= 120) $score += 10;
        else $score += 5;
    }

    // Lifestyle Risk (35% weight)
    if ($data['smoking'] === 'yes') $score += 15;
    if ($data['alcohol'] === 'high') $score += 10;
    elseif ($data['alcohol'] === 'moderate') $score += 7;
    elseif ($data['alcohol'] === 'low') $score += 3;

    if ($data['activity_mins'] < 30) $score += 10;
    elseif ($data['activity_mins'] < 150) $score += 5;

    if ($data['sleep_hours'] < 6) $score += 5;
    if ($data['stress_level'] === 'high') $score += 5;

    return min(100, $score); // Cap at 100
}

function assess_lifestyle_risk($data) {
    // Get rule-based prediction
    $risk_score = get_risk_prediction($data);

    // Risk level based on score
    if ($risk_score >= 70) {
        $level = 'High';
    } elseif ($risk_score >= 40) {
        $level = 'Moderate';
    } else {
        $level = 'Low';
    }

    // Disease mapping based on risk factors
    $diseases = [];
    if ($data['bmi'] >= 30) $diseases[] = 'Obesity';
    if ($data['bmi'] >= 25) $diseases[] = 'Overweight-related risks';
    if ($data['smoking'] === 'yes' || $data['alcohol'] !== 'none' || $data['activity_mins'] < 30) $diseases[] = 'Heart disease risk';
    if ((!empty($data['systolic_bp']) && $data['systolic_bp'] >= 140) || $data['stress_level'] === 'high') $diseases[] = 'Hypertension risk';
    if (!empty($data['sugar_fasting']) && $data['sugar_fasting'] >= 100) $diseases[] = 'Diabetes risk';
    if ($data['stress_level'] === 'high' || $data['sleep_hours'] < 5) $diseases[] = 'Mental health/stress concerns';
    if ($data['smoking'] === 'yes') $diseases[] = 'Lung cancer risk';
    if ($data['smoking'] === 'yes') $diseases[] = 'COPD risk';
    if ($data['alcohol'] === 'moderate' || $data['alcohol'] === 'high') $diseases[] = 'Liver disease risk';
    if ($data['alcohol'] === 'moderate' || $data['alcohol'] === 'high') $diseases[] = 'Pancreatitis risk';
    if ($data['junk_freq'] === 'often') $diseases[] = 'Digestive disorders risk';
    if ($data['stress_level'] === 'high') $diseases[] = 'Depression risk';
    if ($data['stress_level'] === 'high') $diseases[] = 'Anxiety risk';
    if ($data['sleep_hours'] < 6) $diseases[] = 'Insomnia risk';
    if ($data['activity_mins'] < 60) $diseases[] = 'Osteoporosis risk';
    if ($data['fruits_veggies_servings'] < 3) $diseases[] = 'Nutrient deficiency risk';
    if ($data['water_liters'] < 2) $diseases[] = 'Dehydration risk';
    if ($data['screen_hours'] > 8) $diseases[] = 'Eye problems risk';
    if ($data['age'] > 50) $diseases[] = 'Age-related diseases risk';
    if ($data['bmi'] >= 25 || $data['activity_mins'] < 30) $diseases[] = 'Stroke risk';
    if (!empty($data['sugar_fasting']) && $data['sugar_fasting'] >= 100) $diseases[] = 'Kidney disease risk';
    if ($data['smoking'] === 'yes') $diseases[] = 'Throat cancer risk';
    if ($data['alcohol'] === 'moderate' || $data['alcohol'] === 'high') $diseases[] = 'Gout risk';
    if ($data['stress_level'] === 'high') $diseases[] = 'Migraine risk';
    if ($data['sleep_hours'] < 6) $diseases[] = 'Sleep apnea risk';

    $recs = [];
    if ($data['bmi'] >= 25) $recs[] = 'Consider weight management: increase activity, reduce caloric intake.';
    if ($data['activity_mins'] < 150) $recs[] = 'Aim for at least 150 mins of moderate exercise per week.';
    if ($data['sleep_hours'] < 7) $recs[] = 'Improve sleep hygiene: 7-9 hours nightly.';
    if ($data['smoking'] === 'yes') $recs[] = 'Seek support for smoking cessation.';
    if ($data['alcohol'] !== 'none') $recs[] = 'Limit alcohol consumption.';
    if ($data['fruits_veggies_servings'] < 3) $recs[] = 'Increase fruits & vegetables to 5 servings/day.';
    if ($data['water_liters'] < 2) $recs[] = 'Drink at least 2 liters of water daily.';
    if ($data['stress_level'] === 'high') $recs[] = 'Practice stress management techniques like meditation.';
    if ($data['screen_hours'] > 8) $recs[] = 'Reduce screen time and take regular breaks.';

    return [
        'score' => (int)round($risk_score),
        'level' => $level,
        'diseases' => implode(', ', array_unique($diseases)),
        'recommendations' => implode(' ', $recs)
    ];
}
?>
