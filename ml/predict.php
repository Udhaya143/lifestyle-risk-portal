<?php
require_once '../config/db.php';

// Just set a dummy prediction
$res = $mysqli->query("SELECT * FROM assessments WHERE ml_prediction IS NULL OR ml_prediction=''");

while ($row = $res->fetch_assoc()) {
    $ml_prediction = "Test Prediction";

    $stmt = $mysqli->prepare("UPDATE assessments SET ml_prediction=? WHERE id=?");
    $stmt->bind_param("si", $ml_prediction, $row['id']);
    $stmt->execute();

    echo "Updated ID {$row['id']} → $ml_prediction<br>";
}
echo "Done!";
?>
