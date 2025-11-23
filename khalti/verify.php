<?php
session_start();
error_reporting(0);
require_once('../include/config.php');
// Load Khalti keys from env or config file
@include_once __DIR__ . '/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['uid']) || strlen((string)$_SESSION['uid']) === 0) {
  http_response_code(401);
  echo json_encode(['success'=>false,'message'=>'Not authenticated']);
  exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$token = isset($input['token']) ? $input['token'] : '';
$amount = isset($input['amount']) ? (int)$input['amount'] : 0; // in paisa
$booking_id = isset($input['booking_id']) ? (int)$input['booking_id'] : 0;

if (!$token || $amount<=0 || $booking_id<=0) {
  http_response_code(400);
  echo json_encode(['success'=>false,'message'=>'Invalid request']);
  exit();
}

try {
  $uid = (int)$_SESSION['uid'];
  // Fetch booking and price
  $sql = "SELECT t1.id, t2.Price AS price FROM tblbooking t1 JOIN tbladdpackage t2 ON t1.package_id=t2.id WHERE t1.id=:bid AND t1.userid=:uid";
  $q = $dbh->prepare($sql);
  $q->bindParam(':bid',$booking_id,PDO::PARAM_INT);
  $q->bindParam(':uid',$uid,PDO::PARAM_INT);
  $q->execute();
  $row = $q->fetch(PDO::FETCH_ASSOC);
  if (!$row) {
    http_response_code(404);
    echo json_encode(['success'=>false,'message'=>'Booking not found']);
    exit();
  }
  $expected_npr = (int)$row['price'];

  // Verify with Khalti
  $verify_url = 'https://khalti.com/api/v2/payment/verify/';
  $payload = json_encode(['token'=>$token,'amount'=>$amount]);
  // Prefer environment secret; fallback to config constant
  $secretKey = getenv('KHALTI_SECRET_KEY');
  if (!$secretKey && defined('KHALTI_SECRET_KEY')) {
    $secretKey = KHALTI_SECRET_KEY;
  }
  if (!$secretKey || stripos($secretKey, 'xxxx') !== false || $secretKey === 'PUT_YOUR_SECRET_KEY_HERE') {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>'Khalti secret key not configured']);
    exit();
  }
  $ch = curl_init($verify_url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Key ' . $secretKey,
    'Content-Type: application/json'
  ]);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
  $resp = curl_exec($ch);
  $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $err = curl_error($ch);
  curl_close($ch);
  if ($err || $http < 200 || $http >= 300) {
    http_response_code(502);
    echo json_encode(['success'=>false,'message'=>'Gateway error']);
    exit();
  }
  $data = json_decode($resp,true);
  if (!isset($data['state']) || $data['state']['name'] !== 'Completed') {
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>'Payment not completed','data'=>$data]);
    exit();
  }
  // Amount check
  if ($amount !== $expected_npr * 100) {
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>'Amount mismatch']);
    exit();
  }

  // Record payment
  $dbh->beginTransaction();
  $ptype = 'Full Payment';
  $amt = $expected_npr;
  $ins = $dbh->prepare("INSERT INTO tblpayment (bookingID, paymentType, payment, payment_date) VALUES (:bid,:ptype,:amt,NOW())");
  $ins->bindParam(':bid',$booking_id,PDO::PARAM_INT);
  $ins->bindParam(':ptype',$ptype,PDO::PARAM_STR);
  $ins->bindParam(':amt',$amt,PDO::PARAM_STR);
  $ins->execute();

  $upd = $dbh->prepare("UPDATE tblbooking SET payment=:amt, paymentType=:ptype WHERE id=:bid");
  $upd->bindParam(':amt',$amt,PDO::PARAM_STR);
  $upd->bindParam(':ptype',$ptype,PDO::PARAM_STR);
  $upd->bindParam(':bid',$booking_id,PDO::PARAM_INT);
  $upd->execute();

  $dbh->commit();
  // Send receipt email to user (best-effort)
  @include_once __DIR__ . '/../include/mailer.php';
  // Fetch user email/name
  $u = $dbh->prepare('SELECT fname, email FROM tbluser WHERE id = :uid');
  $u->bindParam(':uid',$uid,PDO::PARAM_INT);
  $u->execute();
  $urow = $u->fetch(PDO::FETCH_ASSOC);
  if ($urow && function_exists('sendMail')) {
    $toEmail = $urow['email'];
    $toName = $urow['fname'];
    $subject = 'Payment Received - FIT TRACK HUB';
    $body = '<div style="font-family:Arial,sans-serif;font-size:14px;line-height:1.6">'
      .'<h2 style="margin:0 0 10px;color:#111827;">Payment Successful</h2>'
      .'<p>Hi '.htmlentities($toName).',</p>'
      .'<p>We have received your payment of <strong>Rs. '.htmlentities((string)$amt).'</strong> for booking ID <strong>'.htmlentities((string)$booking_id).'</strong>.</p>'
      .'<p>Thank you for choosing FIT TRACK HUB.</p>'
      .'<p style="margin-top:18px;color:#6b7280;">— FIT TRACK HUB Team</p>'
      .'</div>';
    @sendMail($toEmail, $subject, $body, $toName);
  }
  echo json_encode(['success'=>true,'message'=>'Payment successful']);
} catch (Exception $e) {
  if ($dbh->inTransaction()) $dbh->rollBack();
  http_response_code(500);
  echo json_encode(['success'=>false,'message'=>'Server error']);
}
?>