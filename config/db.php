
<?php
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'lifestyle_portal';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    die('MySQL connect error: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');
?>
