
<?php
require_once '../includes/auth.php';
include '../includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card p-4 shadow-sm">
      <h3 class="mb-3">New Assessment</h3>
      <form method="post" action="save_health.php">
        <div class="row">
          <div class="col-md-4 mb-3"><label class="form-label">Age</label><input type="number" name="age" class="form-control" min="15" max="100" required></div>
          <div class="col-md-4 mb-3"><label class="form-label">Height (cm)</label><input type="number" step="0.1" name="height_cm" class="form-control" max="300" oninput="calcBMI()" required></div>
          <div class="col-md-4 mb-3"><label class="form-label">Weight (kg)</label><input type="number" step="0.1" name="weight_kg" class="form-control" max="200" oninput="calcBMI()" required></div>
        </div>

        <div class="row">
          <div class="col-md-4 mb-3"><label class="form-label">Sleep hours/day</label><input type="number" step="0.1" name="sleep_hours" class="form-control" required></div>
          <div class="col-md-4 mb-3"><label class="form-label">Water intake (liters/day)</label><input type="number" step="0.1" name="water_liters" class="form-control" required></div>
          <div class="col-md-4 mb-3"><label class="form-label">Physical activity (mins/day)</label><input type="number" name="activity_mins" class="form-control" required></div>
        </div>

        <div class="row">
          <div class="col-md-4 mb-3"><label class="form-label">Screen time (hours/day)</label><input type="number" step="0.1" name="screen_hours" class="form-control" required></div>
          <div class="col-md-4 mb-3"><label class="form-label">Smoking</label><select name="smoking" class="form-select"><option value="no">No</option><option value="yes">Yes</option></select></div>
          <div class="col-md-4 mb-3"><label class="form-label">Alcohol</label><select name="alcohol" class="form-select"><option value="none">None</option><option value="low">Low</option><option value="moderate">Moderate</option><option value="high">High</option></select></div>
        </div>

        <div class="row">
          <div class="col-md-4 mb-3"><label class="form-label">Stress level</label><select name="stress_level" class="form-select"><option value="low">Low</option><option value="moderate" selected>Moderate</option><option value="high">High</option></select></div>
          <div class="col-md-4 mb-3"><label class="form-label">Junk food frequency</label><select name="junk_freq" class="form-select"><option value="never">Never</option><option value="rarely">Rarely</option><option value="sometimes" selected>Sometimes</option><option value="often">Often</option></select></div>
          <div class="col-md-4 mb-3"><label class="form-label">Fruits & Veg servings/day</label><input type="number" name="fruits_veggies_servings" class="form-control" min="0" value="0"></div>
        </div>

        <div class="row">
          <div class="col-md-4 mb-3"><label class="form-label">Systolic BP</label><input type="number" name="systolic_bp" class="form-control"></div>
          <div class="col-md-4 mb-3"><label class="form-label">Diastolic BP</label><input type="number" name="diastolic_bp" class="form-control"></div>
          <div class="col-md-4 mb-3"><label class="form-label">Fasting sugar (mg/dL)</label><input type="number" step="0.1" name="sugar_fasting" class="form-control"></div>
        </div>

        <div class="mb-3"><small class="text-muted">Estimated BMI: <span id="bmi_preview">0</span></small></div>
        <button class="btn btn-primary">Save & Assess</button>
      </form>
    </div>
  </div>
</div>

<script>
function calcBMI() {
    const height = parseFloat(document.querySelector('input[name="height_cm"]').value);
    const weight = parseFloat(document.querySelector('input[name="weight_kg"]').value);

    if (height > 0 && weight > 0) {
        const bmi = (weight / ((height / 100) ** 2)).toFixed(1);
        document.getElementById('bmi_preview').textContent = bmi;
    } else {
        document.getElementById('bmi_preview').textContent = '0';
    }
}
</script>

<?php include '../includes/footer.php'; ?>
