
<?php
session_start();
if (!empty($_SESSION['user_id'])) {
    header('Location: /lifestyle-risk-portal-full/modules/dashboard.php');
} else {
    header('Location: /lifestyle-risk-portal-full/modules/login.php');
}
exit;
?>
