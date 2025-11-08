
<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
$user_id = $_SESSION['user_id'];
// latest assessment
$stmt = $mysqli->prepare('SELECT id, bmi, risk_score, risk_level, diseases, created_at FROM assessments WHERE user_id=? ORDER BY created_at DESC LIMIT 5');
$stmt->bind_param('i',$user_id); $stmt->execute(); $res = $stmt->get_result();
include '../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Hello, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h3>
  <a class="btn btn-success" href="health_form.php">+ New Assessment</a>
</div>
<div class="row">
  <div class="col-12">
    <div class="card p-3 mb-3"><strong>Recent assessments</strong></div>
  </div>
  <?php while($r = $res->fetch_assoc()): ?>
    <div class="col-md-6">
      <div class="card p-3 mb-2 shadow-sm">
        <div class="d-flex justify-content-between">
          <div><div><strong>BMI:</strong> <?php echo $r['bmi']; ?></div><div><strong>Score:</strong> <?php echo $r['risk_score']; ?></div></div>
          <span class="badge <?php echo $r['risk_level']==='High'?'text-bg-danger':($r['risk_level']==='Moderate'?'text-bg-warning':'text-bg-success'); ?>"><?php echo $r['risk_level']; ?></span>
        </div>
        <div class="mt-2 small text-muted"><?php echo $r['created_at']; ?></div>
        <a class="btn btn-sm btn-outline-primary mt-2" href="risk_result.php?id=<?php echo $r['id']; ?>">View</a>
      </div>
    </div>
  <?php endwhile; ?>
</div>
<?php include '../includes/footer.php'; ?>
