<?php
// Collect inputs
$bmi      = $_POST['bmi']      ?? null;
$bp       = $_POST['bp']       ?? null;
$smoking  = $_POST['smoking']  ?? null;
$exercise = $_POST['exercise'] ?? null;
$diet     = $_POST['diet']     ?? null;

// Validate
if ($bmi === null || $bp === null || $smoking === null || $exercise === null || $diet === null) {
    die("<h2>Error: Missing input values.</h2>");
}

// Paths
$python = "C:/wamp64/www/lifestyle-risk-portal-full/venv/Scripts/python.exe";
$script = "C:/wamp64/www/lifestyle-risk-portal-full/ml/predict_risk.py";

// Run Python ML model
$command   = escapeshellcmd("$python $script $bmi $bp $smoking $exercise $diet");
$ml_output = shell_exec($command);

// Save to DB
$conn = new mysqli("localhost", "root", "", "lifestyle_portal");
$stmt = $conn->prepare("INSERT INTO assessments (bmi, bp, smoking, exercise, diet, ml_output, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("dsiiis", $bmi, $bp, $smoking, $exercise, $diet, $ml_output);
$stmt->execute();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Assessment Result</title>
</head>
<body>
  <h1>Lifestyle Disease Risk Assessment Portal</h1>
  <h2>Assessment Result</h2>

  <p><strong>Date:</strong> <?= date("Y-m-d H:i:s") ?></p>
  <p><strong>BMI:</strong> <?= htmlspecialchars($bmi) ?></p>
  <p><strong>BP:</strong> <?= htmlspecialchars($bp) ?></p>
  <p><strong>Smoking:</strong> <?= $smoking == 1 ? "yes" : "no" ?></p>
  <p><strong>Exercise (per week):</strong> <?= htmlspecialchars($exercise) ?></p>
  <p><strong>Diet Score:</strong> <?= htmlspecialchars($diet) ?></p>

  <h3>ML Prediction</h3>
  <pre><?= htmlspecialchars($ml_output) ?></pre>

</body>
</html>