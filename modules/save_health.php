<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once 'calc_risk_no_ml.php';

$user_id = $_SESSION['user_id'];

$data = [];
$fields = ['age','height_cm','weight_kg','sleep_hours','water_liters','activity_mins','screen_hours','systolic_bp','diastolic_bp','sugar_fasting','smoking','alcohol','stress_level','junk_freq','fruits_veggies_servings'];
foreach ($fields as $f) {
    $data[$f] = $_POST[$f] ?? null;
}

// Type conversions
$data['height_cm'] = (float)$data['height_cm'];
$data['weight_kg'] = (float)$data['weight_kg'];
$data['bmi'] = calculate_bmi($data['height_cm'], $data['weight_kg']);

$data['sleep_hours'] = (float)$data['sleep_hours'];
$data['water_liters'] = (float)$data['water_liters'];
$data['activity_mins'] = (int)$data['activity_mins'];
$data['screen_hours'] = (float)$data['screen_hours'];
$data['systolic_bp'] = $data['systolic_bp'] ? (int)$data['systolic_bp'] : null;
$data['diastolic_bp'] = $data['diastolic_bp'] ? (int)$data['diastolic_bp'] : null;
$data['sugar_fasting'] = $data['sugar_fasting'] ? (float)$data['sugar_fasting'] : null;
$data['fruits_veggies_servings'] = (int)$data['fruits_veggies_servings'];

// Use PHP-only risk assessment
$res = assess_lifestyle_risk($data);

$stmt = $mysqli->prepare("INSERT INTO assessments (user_id,age,height_cm,weight_kg,bmi,sleep_hours,water_liters,activity_mins,screen_hours,systolic_bp,diastolic_bp,sugar_fasting,smoking,alcohol,stress_level,junk_freq,fruits_veggies_servings,risk_score,risk_level,diseases,recommendations) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

$stmt->bind_param(
    'iiddddiiiiddsssiidsss',   // 21 characters (spaces added for readability)
    $user_id,
    $data['age'],
    $data['height_cm'],
    $data['weight_kg'],
    $data['bmi'],
    $data['sleep_hours'],
    $data['water_liters'],
    $data['activity_mins'],
    $data['screen_hours'],
    $data['systolic_bp'],
    $data['diastolic_bp'],
    $data['sugar_fasting'],
    $data['smoking'],
    $data['alcohol'],
    $data['stress_level'],
    $data['junk_freq'],
    $data['fruits_veggies_servings'],
    $res['score'],
    $res['level'],
    $res['diseases'],
    $res['recommendations']
);

if ($stmt->execute()) {
    $assessment_id = $stmt->insert_id;
    header('Location: /lifestyle-risk-portal-full/modules/risk_result.php?id=' . $assessment_id);
    exit;
} else {
    die('DB save error: ' . $stmt->error);
}
?>
