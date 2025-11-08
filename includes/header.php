
<?php if (session_status()===PHP_SESSION_NONE) session_start(); ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Lifestyle Risk Portal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
  <script src="../assets/js/app.js" defer></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
  <div class="container">
    <a class="navbar-brand" href="../modules/dashboard.php">Lifestyle Disease Risk Assessment Portal</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <?php if (!empty($_SESSION['user_id'])): ?>
          <li class="nav-item"><a class="nav-link" href="../index.html">🏠</a></li>
          <li class="nav-item"><a class="nav-link" href="../modules/health_form.php">New Assessment</a></li>
          <li class="nav-item"><a class="nav-link" href="../modules/history.php">History</a></li>
          <li class="nav-item"><a class="nav-link" href="../modules/charts.php">Charts</a></li>
          <li class="nav-item"><a class="nav-link" href="../modules/logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="../modules/login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="../modules/register.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container py-4">
