
<?php
require_once '../config/db.php';
if (session_status()===PHP_SESSION_NONE) session_start();
$errors=[];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $mysqli->prepare('SELECT id,name,password FROM users WHERE email=? LIMIT 1');
    $stmt->bind_param('s',$email); $stmt->execute(); $stmt->bind_result($id,$name,$hash);
    if ($stmt->fetch() && password_verify($password,$hash)) {
        $_SESSION['user_id']=$id; $_SESSION['user_name']=$name;
        header('Location: ../index.php'); exit;
    } else { $errors[]='Invalid credentials.'; }
}
include '../includes/header.php';
?>
<div class="row justify-content-center"><div class="col-md-6"><div class="card p-4 shadow-sm">
  <h3>Login</h3>
  <?php if ($errors): ?><div class="alert alert-danger"><?php echo implode('<br>', $errors); ?></div><?php endif; ?>
  <form method="post">
    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
    <button class="btn btn-primary w-100">Login</button>
  </form>
  <p class="mt-3">No account? <a href="register.php">Register</a></p>
</div></div></div>
<?php include '../includes/footer.php'; ?>
