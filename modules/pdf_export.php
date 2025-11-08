<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

$id = (int)($_GET['id'] ?? 0);
$user_id = $_SESSION['user_id'];
$stmt = $mysqli->prepare('SELECT a.*, u.name, u.email FROM assessments a JOIN users u ON u.id=a.user_id WHERE a.id=? AND a.user_id=? LIMIT 1');
$stmt->bind_param('ii',$id,$user_id); 
$stmt->execute(); 
$res = $stmt->get_result();

if ($res->num_rows===0) { 
    header('Location: history.php'); 
    exit; 
}
$R = $res->fetch_assoc();

// Build HTML
$html = '<!doctype html><html><head><meta charset="utf-8"><title>Report</title>
<style>body{font-family:Arial,Helvetica,sans-serif;} .h{font-size:18px;font-weight:700;}</style></head><body>';
$html .= '<div class="h">Lifestyle Risk Report</div>';
$html .= '<p><strong>Name:</strong> '.htmlspecialchars($R['name']).' | <strong>Date:</strong> '.htmlspecialchars($R['created_at']).'</p>';
$html .= '<p><strong>BMI:</strong> '.htmlspecialchars($R['bmi']).' | <strong>Score:</strong> '.htmlspecialchars($R['risk_score']).' ('.htmlspecialchars($R['risk_level']).')</p>';
$html .= '<h4>Possible conditions</h4><p>'.nl2br(htmlspecialchars($R['diseases'])).'</p>';
$html .= '<h4>Recommendations</h4><p>'.nl2br(htmlspecialchars($R['recommendations'])).'</p>';
$html .= '</body></html>';

// Use Dompdf
require_once __DIR__ . '/../dompdf/autoload.inc.php';  // path to your dompdf folder
use Dompdf\Dompdf;

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Lifestyle_Report.pdf", ["Attachment" => 1]);

