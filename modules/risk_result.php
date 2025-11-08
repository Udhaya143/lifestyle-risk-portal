<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$id = (int)($_GET['id'] ?? 0);
$user_id = $_SESSION['user_id'];

// Fetch record
$stmt = $mysqli->prepare('SELECT * FROM assessments WHERE id=? AND user_id=? LIMIT 1');
$stmt->bind_param('ii', $id, $user_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    header('Location: dashboard.php');
    exit;
}

$R = $res->fetch_assoc();

/* -------------------------------------
   ✅ Rule-based "ML Prediction"
------------------------------------- */
function predictRisk($R) {
    $risk_score = 0;
    $possible_conditions = [];
    $analysis = [];
    $recommendations = [];

    // BMI
    if ($R['bmi'] < 18.5) {
        $risk_score += 15;
        $possible_conditions[] = "Nutritional deficiency";
        $possible_conditions[] = "Weakened immunity";
        $analysis[] = "Underweight may weaken immunity and cause fatigue.";
        $recommendations[] = "Increase protein and balanced calorie intake.";
    } elseif ($R['bmi'] > 30) {
        $risk_score += 25;
        $possible_conditions[] = "Obesity";
        $possible_conditions[] = "Metabolic syndrome";
        $possible_conditions[] = "Sleep apnea";
        $analysis[] = "High BMI increases risk of diabetes, heart disease, and joint problems.";
        $recommendations[] = "Adopt weight management with diet & regular exercise.";
    }

    // Blood pressure
    if ($R['systolic_bp'] < 90 || $R['diastolic_bp'] < 60) {
        $risk_score += 15;
        $possible_conditions[] = "Hypotension";
        $analysis[] = "Low blood pressure can cause dizziness and organ underperfusion.";
        $recommendations[] = "Maintain hydration and monitor BP regularly.";
    }
    if ($R['systolic_bp'] > 140 || $R['diastolic_bp'] > 90) {
        $risk_score += 25;
        $possible_conditions[] = "Hypertension";
        $possible_conditions[] = "Heart disease";
        $possible_conditions[] = "Kidney disease";
        $analysis[] = "High BP increases risk of stroke, kidney disease, and heart attack.";
        $recommendations[] = "Reduce salt, manage stress, and check BP frequently.";
    }

    // Sugar
    if ($R['sugar_fasting'] < 70) {
        $risk_score += 15;
        $possible_conditions[] = "Hypoglycemia";
        $analysis[] = "Low sugar may indicate hypoglycemia, dangerous if frequent.";
        $recommendations[] = "Eat small frequent meals and consult a doctor if symptoms persist.";
    }
    if ($R['sugar_fasting'] > 125) {
        $risk_score += 25;
        $possible_conditions[] = "Type 2 Diabetes";
        $possible_conditions[] = "Pre-diabetes";
        $possible_conditions[] = "Pancreatic disorder";
        $analysis[] = "High fasting sugar indicates possible diabetes or insulin resistance.";
        $recommendations[] = "Monitor sugar, adopt low-carb diet, and exercise regularly.";
    }

    // Lifestyle
    if ($R['smoking'] === 'yes') {
        $risk_score += 20;
        $possible_conditions[] = "Lung cancer";
        $possible_conditions[] = "COPD (Chronic Obstructive Pulmonary Disease)";
        $possible_conditions[] = "Cardiovascular disease";
        $analysis[] = "Smoking damages lungs, raises cancer & heart disease risk.";
        $recommendations[] = "Quit smoking immediately to reduce long-term health risks.";
    }
    if ($R['alcohol'] === 'yes') {
        $risk_score += 10;
        $possible_conditions[] = "Liver disease";
        $possible_conditions[] = "Pancreatitis";
        $possible_conditions[] = "High blood pressure";
        $analysis[] = "Alcohol increases risk of liver and heart disease.";
        $recommendations[] = "Reduce or avoid alcohol consumption.";
    }
    if ($R['activity_mins'] < 30) {
        $risk_score += 10;
        $possible_conditions[] = "Sedentary lifestyle complications";
        $possible_conditions[] = "Obesity risk";
        $possible_conditions[] = "Joint stiffness";
        $analysis[] = "Low activity may cause obesity and metabolic syndrome.";
        $recommendations[] = "Aim for at least 30–45 minutes of daily activity.";
    }
    if ($R['sleep_hours'] < 6) {
        $risk_score += 10;
        $possible_conditions[] = "Sleep deprivation";
        $possible_conditions[] = "Depression risk";
        $possible_conditions[] = "Cognitive decline";
        $analysis[] = "Poor sleep affects immunity, memory, and heart health.";
        $recommendations[] = "Maintain 7–8 hrs/day of quality sleep.";
    }
    if (!empty($R['junk_freq']) && $R['junk_freq'] !== 'none') {
        $risk_score += 10;
        $possible_conditions[] = "Digestive issues";
        $possible_conditions[] = "High cholesterol";
        $possible_conditions[] = "Gastric reflux";
        $analysis[] = "Frequent junk food increases obesity and cholesterol risk.";
        $recommendations[] = "Reduce junk food, replace with fruits & vegetables.";
    }

    // Final prediction
    if ($risk_score >= 60) {
        $risk_level = "High";
        $confidence = 90;
    } elseif ($risk_score >= 30) {
        $risk_level = "Moderate";
        $confidence = 75;
    } else {
        $risk_level = "Low";
        $confidence = 60;
    }

    return [
        "risk_level" => $risk_level,
        "confidence" => $confidence,
        "diseases" => array_unique($possible_conditions),
        "analysis" => $analysis,
        "recommendations" => $recommendations,
        "risk_score" => $risk_score
    ];
}

// ✅ If no ML prediction in DB, compute it
if (empty($R['ml_prediction'])) {
    $R['ml_prediction'] = json_encode(predictRisk($R));
}

include '../includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card p-4 shadow-sm">
      <div class="d-flex justify-content-between">
        <div>
          <h4>Assessment Result</h4>
          <div class="small text-muted"><?php echo $R['created_at']; ?></div>
        </div>
        <div>
          <?php $decoded = json_decode($R['ml_prediction'], true); ?>
          <span class="badge 
            <?php echo $decoded['risk_level']==='High' ? 'text-bg-danger' : ($decoded['risk_level']==='Moderate' ? 'text-bg-warning' : 'text-bg-success'); ?>">
            <?php echo $decoded['risk_level']; ?>
          </span>
        </div>
      </div>
      <hr>

      <div class="row">
        <div class="col-md-6">
          <ul class="list-group list-group-flush">
            <li class="list-group-item"><strong>BMI:</strong> <?php echo $R['bmi']; ?></li>
            <li class="list-group-item"><strong>Age:</strong> <?php echo $R['age']; ?></li>
            <li class="list-group-item"><strong>BP:</strong> <?php echo $R['systolic_bp']; ?>/<?php echo $R['diastolic_bp']; ?></li>
            <li class="list-group-item"><strong>Fasting Sugar:</strong> <?php echo $R['sugar_fasting']; ?></li>
            <li class="list-group-item"><strong>Score:</strong> <?php echo $decoded['risk_score']; ?>/100</li>
          </ul>
        </div>
        <div class="col-md-6">
          <ul class="list-group list-group-flush">
            <li class="list-group-item"><strong>Smoking:</strong> <?php echo $R['smoking']; ?></li>
            <li class="list-group-item"><strong>Alcohol:</strong> <?php echo $R['alcohol']; ?></li>
            <li class="list-group-item"><strong>Activity (mins/day):</strong> <?php echo $R['activity_mins']; ?></li>
            <li class="list-group-item"><strong>Sleep hrs/day:</strong> <?php echo $R['sleep_hours']; ?></li>
            <li class="list-group-item"><strong>Junk food:</strong> <?php echo $R['junk_freq']; ?></li>
          </ul>
        </div>
      </div>

      <div class="mt-3">
        <h5>ML Prediction</h5>
        <p>
          <strong>Risk Level:</strong> <?php echo htmlspecialchars($decoded['risk_level']); ?><br>
          <strong>ML Predicted Possible Conditions:</strong> <?php echo htmlspecialchars(implode(", ", $decoded['diseases'])); ?><br>
          <strong>Confidence:</strong> <?php echo htmlspecialchars($decoded['confidence']); ?>%
        </p>

        <h5>Detailed Health Analysis</h5>
        <ul>
          <?php foreach ($decoded['analysis'] as $a) { echo "<li>".htmlspecialchars($a)."</li>"; } ?>
        </ul>

        <h5>Personalized Recommendations</h5>
        <ul>
          <?php foreach ($decoded['recommendations'] as $rec) { echo "<li>".htmlspecialchars($rec)."</li>"; } ?>
        </ul>
      </div>

      <div class="mt-3 d-flex gap-2">
        <a class="btn btn-secondary" href="dashboard.php">Back</a>
        <button onclick="window.print()" class="btn btn-outline-primary">Print / Save as PDF</button>
        <a class="btn btn-outline-success" href="pdf_export.php?id=<?php echo $R['id']; ?>">Download PDF (server)</a>
      </div>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
