<?php
echo "=== ML Integration Test ===\n\n";

echo "1. Testing Python availability...\n";
$python_test = shell_exec('python --version 2>&1');
if (strpos($python_test, 'Python') !== false) {
    echo "✓ Python found: " . trim($python_test) . "\n";
} else {
    echo "✗ Python not found. Please install Python 3.8+\n";
}

echo "\n2. Testing ML model files...\n";
$model_files = [
    'ml/model/disease_risk_model.pkl',
    'ml/model/scaler.pkl',
    'ml/predict_risk.py'
];

foreach ($model_files as $file) {
    if (file_exists($file)) {
        echo "✓ $file exists\n";
    } else {
        echo "✗ $file missing\n";
    }
}

echo "\n3. Testing ML prediction...\n";
$test_data = [
    'bmi' => 25.5,
    'systolic_bp' => 120,
    'smoking' => 'no',
    'alcohol' => 'none',
    'activity_mins' => 30
];

$cmd = sprintf(
    "python ml/predict_risk.py %.2f %d %d %d %d",
    $test_data['bmi'],
    $test_data['systolic_bp'],
    $test_data['smoking'] === 'yes' ? 1 : 0,
    ['none' => 0, 'low' => 1, 'moderate' => 2, 'high' => 3][$test_data['alcohol']],
    $test_data['activity_mins'] >= 150 ? 3 : ($test_data['activity_mins'] >= 60 ? 2 : 1)
);

echo "Command: $cmd\n";
$output = shell_exec($cmd . ' 2>&1');
$prediction = trim($output);

if (is_numeric($prediction)) {
    echo "✓ ML prediction successful: $prediction\n";
    echo "✓ Risk level: " . ($prediction == 0 ? 'Low' : 'High') . "\n";
} else {
    echo "✗ ML prediction failed: $output\n";
}

echo "\n4. Testing database connection...\n";
require_once 'config/db.php';
if ($mysqli->ping()) {
    echo "✓ Database connection successful\n";
    $result = $mysqli->query("SELECT COUNT(*) as count FROM users");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "✓ Users table accessible (found {$row['count']} users)\n";
    }
} else {
    echo "✗ Database connection failed\n";
}

echo "\n=== Test Complete ===\n";
echo "If all tests pass, the system should be working correctly!\n";
?>
