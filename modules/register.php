
<?php
require_once '../config/db.php';
if (session_status()===PHP_SESSION_NONE) session_start();
$errors = [];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$name || !$email || !$password) $errors[] = 'Name, email and password required.';
    if (empty($errors)) {
        $stmt = $mysqli->prepare('SELECT id FROM users WHERE email=? LIMIT 1');
        $stmt->bind_param('s',$email); $stmt->execute(); $stmt->store_result();
        if ($stmt->num_rows>0) { $errors[]='Email already registered.'; }
        else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare('INSERT INTO users (name,email,password) VALUES (?,?,?)');
            $stmt->bind_param('sss',$name,$email,$hash);
            if ($stmt->execute()) {
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['user_name'] = $name;
                header('Location: /lifestyle-risk-portal-full/index.html'); exit;
            } else { $errors[]='Registration failed.'; }
        }
    }
}
include '../includes/header.php';
?>
<div class="row justify-content-center">
<div class="col-md-6">
<div class="card p-4 shadow-sm">
  <h3>Create account</h3>
  <?php if ($errors): ?><div class="alert alert-danger"><?php echo implode('<br>', $errors); ?></div><?php endif; ?>
  <form method="post">
    <div class="mb-3"><label class="form-label">Full name</label><input name="name" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" minlength="8" maxlength="15" required></div>
    <button class="btn btn-primary w-100">Register</button> 
  </form>
  <p class="mt-3">Already have an account? <a href="login.php">Login</a></p>
</div>
</div>
</div>
<?php include '../includes/footer.php'; ?>
