<?php
session_start();
error_reporting(0);
header('Content-Type: application/json');

require_once('include/config.php');

if (!isset($_SESSION['uid']) || strlen((string)$_SESSION['uid']) === 0) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$raw = file_get_contents('php://input');
$input = json_decode($raw, true);

$bookingId = isset($input['booking_id']) ? (int)$input['booking_id'] : 0;

if ($bookingId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid booking id']);
    exit();
}

try {
    $uid = (int)$_SESSION['uid'];

    // Verify booking belongs to user and fetch expected amount
    $q = $dbh->prepare("SELECT t1.id, t2.Price AS price FROM tblbooking t1 JOIN tbladdpackage t2 ON t1.package_id=t2.id WHERE t1.id=:bid AND t1.userid=:uid");
    $q->bindParam(':bid', $bookingId, PDO::PARAM_INT);
    $q->bindParam(':uid', $uid, PDO::PARAM_INT);
    $q->execute();
    $row = $q->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Booking not found']);
        exit();
    }

    $expectedAmount = (float)$row['price'];

    $dbh->beginTransaction();

    // Mark booking as cash pending (do not finalize payment amount here)
    $pendingLabel = 'Cash (Pending)';
    $upd = $dbh->prepare("UPDATE tblbooking SET paymentType = :ptype WHERE id = :bid AND userid = :uid");
    $upd->bindParam(':ptype', $pendingLabel, PDO::PARAM_STR);
    $upd->bindParam(':bid', $bookingId, PDO::PARAM_INT);
    $upd->bindParam(':uid', $uid, PDO::PARAM_INT);
    $upd->execute();

    // Log a pending payment row with zero amount to reflect pending collection
    $zero = 0.0;
    $ins = $dbh->prepare("INSERT INTO tblpayment (bookingID, paymentType, payment, payment_date) VALUES (:bid, :ptype, :amt, NOW())");
    $ins->bindParam(':bid', $bookingId, PDO::PARAM_INT);
    $ins->bindParam(':ptype', $pendingLabel, PDO::PARAM_STR);
    $ins->bindParam(':amt', $zero, PDO::PARAM_STR);
    $ins->execute();

    $dbh->commit();
    // Send pending cash notification (best-effort)
    @include_once __DIR__ . '/include/mailer.php';
    $u = $dbh->prepare('SELECT u.fname, u.email, t2.titlename AS title FROM tblbooking t1 JOIN tbluser u ON t1.userid=u.id JOIN tbladdpackage t2 ON t1.package_id=t2.id WHERE t1.id=:bid AND t1.userid=:uid');
    $u->bindParam(':bid',$bookingId,PDO::PARAM_INT);
    $u->bindParam(':uid',$uid,PDO::PARAM_INT);
    $u->execute();
    $urow = $u->fetch(PDO::FETCH_ASSOC);
    if ($urow && function_exists('sendMail')) {
        $toEmail = $urow['email'];
        $toName = $urow['fname'];
        $subject = 'Cash Payment Pending - FIT TRACK HUB';
        $body = '<div style="font-family:Arial,sans-serif;font-size:14px;line-height:1.6">'
          .'<h2 style="margin:0 0 10px;color:#111827;">Cash Payment Pending</h2>'
          .'<p>Hi '.htmlentities($toName).',</p>'
          .'<p>Your booking (<strong>'.htmlentities($urow['title']).'</strong>) has been marked as <strong>Cash (Pending)</strong>. '
          .'Please complete the payment of <strong>Rs. '.htmlentities((string)$expectedAmount).'</strong> at the gym reception.</p>'
          .'<p style="margin-top:18px;color:#6b7280;">— FIT TRACK HUB Team</p>'
          .'</div>';
        @sendMail($toEmail, $subject, $body, $toName);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Cash payment marked as pending.',
        'expected_amount' => $expectedAmount
    ]);
} catch (Exception $e) {
    if ($dbh->inTransaction()) $dbh->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
?>


