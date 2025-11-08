<?php
session_start();
require_once '../config/db.php';

// Collect input values from POST (or database if already stored)
$bmi = floatval($_POST['bmi']);
$age = intval($_POST['age']);
$bp_sys = intval($_POST['bp_sys']);
$bp_dia = intval($_POST['bp_dia']);
$fsugar = floatval($_POST['fsugar']);
$score = intval($_POST['score']);
$smoking = $_POST['smoking'];
$alcohol = $_POST['alcohol'];
$activity_mins = intval($_POST['activity']);
$sleep = floatval($_POST['sleep']);
$junk = $_POST['junk'];

// Convert for ML
$smoking_val = ($smoking === 'yes') ? 1 : 0;
$alcohol_map = ['none' => 0, 'low' => 1, 'moderate' => 2, 'high' => 3];
$alcohol_val = $alcohol_map[$alcohol];
$activity_val = ($activity_mins >= 150) ? 3 : (($activity_mins >= 60) ? 2 : 1);

// Run Python ML script
$python = "C:/wamp64/www/lifestyle-risk-portal-full/venv/Scripts/python.exe";
$script = "C:/wamp64/www/lifestyle-risk-portal-full/ml/predict_risk.py";
$cmd = escapeshellcmd("$python $script $bmi $bp_sys $smoking_val $alcohol_val $activity_val");
$output = shell_exec($cmd . " 2>&1");

// Decode ML JSON
$result = json_decode(trim($output), true);
if (!$result) {
    die("<h2>Prediction Error</h2><pre>$output</pre>");
}

// Save to DB (optional)
$user_id = $_SESSION['user_id'];
$stmt = $mysqli->prepare("INSERT INTO risk_history 
    (user_id, bmi, age, bp_sys, bp_dia, fsugar, score, smoking, alcohol, activity, sleep, junk, risk_level, risks, confidence, recommendations) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param(
    "idiiidissidssss",
    $user_id,
    $bmi,
    $age,
    $bp_sys,
    $bp_dia,
    $fsugar,
    $score,
    $smoking,
    $alcohol,
    $activity_mins,
    $sleep,
    $junk,
    $result['Risk Level'],
    implode(", ", $result['Possible Risks']),
    $result['Confidence'],
    $result['Recommendations']
);
$stmt->execute();
$stmt->close();

// Current timestamp
$datetime = date("Y-m-d H:i:s");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Assessment Result</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f5f7fa; }
        .card { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 3px 8px rgba(0,0,0,0.1); max-width: 700px; margin: auto; }
        h2 { margin-top: 0; color: #333; }
        .section { margin-bottom: 20px; }
        .label { font-weight: bold; color: #444; }
        .ml { background: #f0f8ff; padding: 15px; border-radius: 10px; }
    </style>
</head>
<body>
<div class="card">
    <h2>Assessment Result</h2>
    <p><span class="label">Date & Time:</span> <?= htmlspecialchars($datetime) ?></p>
    <p><span class="label">Risk Level:</span> <?= htmlspecialchars($result['Risk Level']) ?></p>
    <p><span class="label">BMI:</span> <?= number_format($bmi, 2) ?></p>
    <p><span class="label">Age:</span> <?= $age ?></p>
    <p><span class="label">BP:</span> <?= $bp_sys ?>/<?= $bp_dia ?></p>
    <p><span class="label">Fasting Sugar:</span> <?= number_format($fsugar, 2) ?></p>
    <p><span class="label">Score:</span> <?= $score ?>/100</p>
    <p><span class="label">Smoking:</span> <?= $smoking ?></p>
    <p><span class="label">Alcohol:</span> <?= $alcohol ?></p>
    <p><span class="label">Activity (mins/day):</span> <?= $activity_mins ?></p>
    <p><span class="label">Sleep (hrs/day):</span> <?= number_format($sleep, 2) ?></p>
    <p><span class="label">Junk food:</span> <?= $junk ?></p>

    <div class="ml">
        <h2>ML Prediction</h2>
        <p><span class="label">Risk Level:</span> <?= htmlspecialchars($result['Risk Level']) ?></p>
        <p><span class="label">Possible Risks:</span> <?= htmlspecialchars(implode(", ", $result['Possible Risks'])) ?></p>
        <p><span class="label">Confidence:</span> <?= htmlspecialchars($result['Confidence']) ?></p>

        <h2>Recommendations</h2>
        <p><?= htmlspecialchars($result['Recommendations']) ?></p>
    </div>
</div>
</body>
</html>
