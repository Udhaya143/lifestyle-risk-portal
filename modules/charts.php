<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
$user_id = $_SESSION['user_id'];
//echo $user_id;

// fetch assessment data
$stmt = $mysqli->prepare('SELECT created_at,bmi,risk_score,activity_mins,sleep_hours,water_liters,screen_hours 
                          FROM assessments WHERE user_id=? ORDER BY created_at ASC');
$stmt->bind_param('i',$user_id);
$stmt->execute();
$res = $stmt->get_result();
$data = [];
while($r = $res->fetch_assoc()) $data[] = $r;

include '../includes/header.php';
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<h3 class="mb-4">📊 Lifestyle Risk Report</h3>
<div class="row">
  <!-- BMI Trend -->
  <div class="col-md-6">
    <div class="card p-3 mb-3 shadow-sm">
      <h5 class="mb-3">📈 BMI Trend</h5>
      <canvas id="bmiChart" height="150"></canvas>
    </div>
  </div>

  <!-- Risk Score Trend -->
  <div class="col-md-6">
    <div class="card p-3 mb-3 shadow-sm">
      <h5 class="mb-3">⚠️ Risk Score Trend</h5>
      <canvas id="scoreChart" height="150"></canvas>
    </div>
  </div>

  <!-- Lifestyle Radar -->
  <div class="col-12">
    <div class="card p-3 mb-3 shadow-sm">
      <h5 class="mb-3">🩺 Latest Lifestyle Balance</h5>
      <canvas id="radarChart" height="200"></canvas>
    </div>
  </div>
</div>

<script>
// Parse PHP data
const data = <?php echo json_encode($data); ?>;

// Extract columns
const labels   = data.map(d=>d.created_at);
const bmi      = data.map(d=>parseFloat(d.bmi));
const score    = data.map(d=>parseInt(d.risk_score));
const activity = data.map(d=>parseInt(d.activity_mins));
const sleep    = data.map(d=>parseFloat(d.sleep_hours));
const water    = data.map(d=>parseFloat(d.water_liters));
const screen   = data.map(d=>parseFloat(d.screen_hours));

// BMI Line Chart
new Chart(document.getElementById('bmiChart'), {
  type: 'line',
  data: {
    labels: labels,
    datasets: [{
      label: 'BMI',
      data: bmi,
      borderColor: '#007bff',
      backgroundColor: 'rgba(0,123,255,0.1)',
      tension: 0.3,
      fill: true,
      pointRadius: 4,
      pointHoverRadius: 6
    }]
  },
  options: { responsive: true, plugins:{ legend:{ display:false } } }
});

// Risk Score Chart
new Chart(document.getElementById('scoreChart'), {
  type: 'line',
  data: {
    labels: labels,
    datasets: [{
      label: 'Risk Score',
      data: score,
      borderColor: '#dc3545',
      backgroundColor: 'rgba(220,53,69,0.1)',
      tension: 0.3,
      fill: true,
      pointRadius: 4,
      pointHoverRadius: 6
    }]
  },
  options: { responsive: true }
});

// Lifestyle Line Chart (latest entry)
if (data.length > 0) {
  const last = data[data.length - 1];
  new Chart(document.getElementById('radarChart'), {
    type: 'line',   // 🔹 changed from 'radar' to 'line'
    data: {
      labels: ['Activity (mins)', 'Sleep (hrs)', 'Water (L/day)', 'Screen (hrs)'],
      datasets: [{
        label: 'Latest Assessment',
        data: [last.activity_mins, last.sleep_hours, last.water_liters, last.screen_hours],
        borderColor: '#28a745',
        backgroundColor: 'rgba(40,167,69,0.2)',
        tension: 0.3,
        fill: true,
        pointRadius: 5,
        pointBackgroundColor: '#28a745'
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: true }
      },
      scales: {
        y: { beginAtZero: true }
      }
    }
  });
}

</script>

<?php include '../includes/footer.php'; ?>
