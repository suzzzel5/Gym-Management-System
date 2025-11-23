<?php session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('include/config.php');
if(strlen($_SESSION["uid"]) == 0) {   
    header('location:login.php');
    exit;
} else {
    $uid = $_SESSION['uid'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Booking Details | FIT TRACK HUB</title>
    <meta charset="UTF-8">
    <meta name="description" content="View your booking details and payment history">
    <meta name="keywords" content="gym, fitness, booking, details">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Local fallback CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" href="css/font-awesome.min.css"/>
    <link rel="stylesheet" href="css/owl.carousel.min.css"/>
    <link rel="stylesheet" href="css/nice-select.css"/>
    <link rel="stylesheet" href="css/magnific-popup.css"/>
    <link rel="stylesheet" href="css/slicknav.min.css"/>
    <link rel="stylesheet" href="css/animate.css"/>
    <link rel="stylesheet" href="css/style.css"/>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        /* Fix header nav visibility specific to this page */
        .header-bottom .main-menu .nav-link { color: var(--text-primary) !important; }
        .header-bottom .main-menu .nav-link.active, .header-bottom .main-menu .nav-link:hover { color: var(--primary-color) !important; }
        :root {
            --primary-color: #6366f1;
            --secondary-color: #f59e0b;
            --accent-color: #ef4444;
            --dark-color: #1f2937;
            --light-color: #f8fafc;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--text-primary);
            background: var(--light-color);
            padding-top: 120px;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            line-height: 1.2;
        }
        
        /* Page Header */
        .page-header {
            background: var(--gradient-primary);
            padding: 60px 0;
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('img/page-top-bg.jpg') center/cover;
            opacity: 0.1;
            z-index: 1;
        }
        
        .page-header-content { position: relative; z-index: 2; }
        .page-title { font-size: 3rem; font-weight: 800; margin-bottom: .5rem; text-shadow: 2px 2px 4px rgba(0,0,0,.3); }
        .page-subtitle { font-size: 1.2rem; opacity: .9; }
        
        /* Fallback: if AOS fails to init, keep content visible */
        [data-aos] { opacity: 1 !important; transform: none !important; }
        
        /* Back Button */
        .btn-back {
            display: inline-flex; align-items: center; gap:10px;
            padding: 12px 24px; background: var(--gradient-primary); color:#fff;
            text-decoration: none; border-radius: 28px; font-weight:700; margin-bottom:30px;
            transition:.25s ease; box-shadow:0 6px 18px rgba(99,102,241,.35);
        }
        .btn-back:hover { transform: translateY(-2px); color:#fff; box-shadow:0 10px 28px rgba(99,102,241,.45); }

        /* Summary */
        .summary-card { display:flex; align-items:center; justify-content:space-between; gap:20px; background:#fff; border:1px solid rgba(0,0,0,.06); border-radius:18px; padding:18px 24px; box-shadow:0 8px 24px rgba(0,0,0,.06); }
        .summary-title { font-size:1.35rem; font-weight:800; color:var(--text-primary); }
        .summary-chips { display:flex; flex-wrap:wrap; gap:8px; margin-top:6px; }
        .chip { display:inline-flex; align-items:center; gap:6px; padding:6px 12px; border-radius:999px; background:#f3f4f6; color:#374151; font-weight:600; font-size:.85rem; }
        .chip-info { background:rgba(99,102,241,.12); color:#4f46e5; }
        .chip-success { background:rgba(16,185,129,.12); color:#059669; }
        .chip-warn { background:rgba(245,158,11,.15); color:#B45309; }
        .summary-price { font-size:1.8rem; font-weight:800; color:var(--primary-color); }
        .summary-date { color:#6b7280; font-weight:600; margin-top:6px; font-size:.9rem; }

        /* Cards */
        .detail-card { background:#fff; border-radius:20px; border:1px solid rgba(99,102,241,.12); box-shadow:0 8px 30px rgba(31,41,55,.08); overflow:hidden; margin-bottom:30px; }
        .detail-card-header { background:linear-gradient(135deg,#667eea,#764ba2); color:#fff; padding:24px 28px; text-align:center; }
        .detail-card-header h3, .detail-card-header h4 { margin:0; font-size:1.6rem; font-weight:800; letter-spacing:.3px; }
        .detail-card-body { padding:28px; }

        /* Info Card */
        .info-card { background:#fff; border:1px solid rgba(0,0,0,.06); border-radius:16px; padding:20px; transition:transform .2s, box-shadow .2s; }
        .info-card:hover { transform:translateY(-4px); box-shadow:0 12px 30px rgba(0,0,0,.08); }
        .info-card h5 { color:var(--primary-color); font-weight:700; margin-bottom:16px; }

        /* Table */
        .table { margin:0; }
        .table thead { background:#f8f9fa; color:#495057; }
        .table thead th { border:0; padding:14px; font-weight:700; letter-spacing:.5px; text-transform:uppercase; }
        .table tbody td { padding:14px; border-bottom:1px solid #eef2f7; }
        .table tbody tr:hover { background:rgba(99,102,241,.05); }

        /* Alerts */
        .alert { border-radius:14px; padding:16px 18px; border:0; box-shadow:0 6px 18px rgba(0,0,0,.08); }
        .alert-danger { background:linear-gradient(135deg,#fee2e2,#fecaca); color:#b91c1c; }

        /* Badges */
        .badge { border-radius:16px; padding:.45rem .8rem; font-weight:800; letter-spacing:.2px; }

        /* Buttons */
        .btn-payment { background:var(--gradient-secondary); border:0; color:#fff; padding:12px 24px; border-radius:28px; font-weight:800; box-shadow:0 10px 25px rgba(240,147,251,.35); transition:.25s ease; }
        .btn-payment:hover { transform:translateY(-2px); box-shadow:0 16px 36px rgba(240,147,251,.45); }
        .btn-payment:disabled { opacity:.6; cursor:not-allowed; }

        .or-divider { position:relative; text-align:center; margin:16px 0; }
        .or-divider:before, .or-divider:after { content:''; position:absolute; top:50%; width:40%; height:1px; background:#e5e7eb; }
        .or-divider:before { left:0; }
        .or-divider:after { right:0; }
        .or-divider span { background:#fff; padding:0 10px; color:#6b7280; font-weight:700; font-size:.85rem; }

        /* Responsive */
        @media (max-width: 768px) {
            .page-title { font-size:2rem; }
            .detail-card-body { padding:20px; }
            .info-card { margin-bottom:18px; }
            .btn-payment { width:100%; margin-top:12px; }
            .summary-card { flex-direction: column; align-items: flex-start; }
            .summary-right { text-align: left !important; width: 100%; }
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <?php include 'include/header.php';?>
    <?php 
    // Prefer env var; fallback to config.php if present; finally allow inline placeholder (replace with your real key)
    $KHALTI_PK = getenv('KHALTI_PUBLIC_KEY') ?: '';
    if(!$KHALTI_PK && file_exists(__DIR__.'/khalti/config.php')) {
        @include_once __DIR__.'/khalti/config.php'; 
        $KHALTI_PK = defined('KHALTI_PUBLIC_KEY') ? KHALTI_PUBLIC_KEY : '';
    }
    if(!$KHALTI_PK) { $KHALTI_PK = 'PUT_YOUR_PUBLIC_KEY_HERE'; }
    ?>
    
    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <div class="page-header-content" data-aos="fade-up">
                <h1 class="page-title">Booking Details</h1>
                <p class="page-subtitle">View your package booking information</p>
            </div>
        </div>
    </section>

    <!-- Booking Details Section -->
    <section style="padding: 60px 0; background: white;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-md-12">
                    <!-- Back button -->
                    <a href="booking-history.php" class="btn-back" data-aos="fade-up">
                        <i class="fas fa-arrow-left me-2"></i>Back to My Bookings
                    </a>
                    <div class="detail-card" data-aos="fade-up" data-aos-delay="100">
                        <div class="detail-card-header">
                            <h3><i class="fas fa-calendar-check me-2"></i>Booking Details</h3>
                        </div>
                        <div class="detail-card-body">
                            <?php
                            $hasError = false;
                            $booking_title = $booking_duration = $booking_price = '';
                            $bookindid = 0;
                            
                            if (!isset($_GET['bookingid']) || !is_numeric($_GET['bookingid'])) {
                                echo '<div class="alert alert-danger text-center">No booking selected.<br>Please select a booking from <a href="booking-history.php">your bookings list</a>.</div>';
                                $hasError = true;
                            }
                            
                            if (!$hasError) {
                                $bookindid = intval($_GET['bookingid']);
                                // Fixed SQL query - removed extra space and fixed syntax
                                $sql = "SELECT t1.id as bookingid, t1.paymentType as paymentType, t3.fname as Name, t3.email as email, 
                                        t1.booking_date as bookingdate, t2.titlename as title, t2.PackageDuratiobn as PackageDuratiobn, 
                                        t2.Price as Price, t2.Description as Description, t4.category_name as category_name, 
                                        t5.PackageName as PackageName 
                                        FROM tblbooking as t1 
                                        LEFT JOIN tbladdpackage as t2 on t1.package_id = t2.id 
                                        LEFT JOIN tbluser as t3 on t1.userid = t3.id 
                                        LEFT JOIN tblcategory as t4 on t2.category = t4.id 
                                        LEFT JOIN tblpackage as t5 on t2.PackageType = t5.id 
                                        WHERE t1.id = :bookindid AND t1.userid = :uid";
                                
                                $query = $dbh->prepare($sql);
                                $query->bindParam(':bookindid', $bookindid, PDO::PARAM_INT);
                                $query->bindParam(':uid', $uid, PDO::PARAM_INT);
                                $query->execute();
                                $results = $query->fetchAll(PDO::FETCH_OBJ);
                                
                                if ($query->rowCount() > 0) {
                                    foreach ($results as $result) {
                                        // Store values for later use
                                        $booking_title = $result->title;
                                        $booking_duration = $result->PackageDuratiobn;
                                        $booking_price = $result->Price;
                            ?>
                            <!-- Booking Summary -->
                            <div class="summary-card mb-4" data-aos="fade-up">
                                <div class="summary-left">
                                    <div class="summary-title"><?php echo htmlentities($booking_title); ?></div>
                                    <div class="summary-chips">
                                        <span class="chip chip-info"><i class="fa fa-clock me-1"></i><?php echo htmlentities($booking_duration); ?></span>
                                        <span class="chip"><i class="fa fa-list-alt me-1"></i><?php echo htmlentities($result->category_name); ?></span>
                                        <span class="chip"><i class="fa fa-tag me-1"></i><?php echo htmlentities($result->PackageName); ?></span>
                                        <?php if(!empty($result->paymentType)) { ?>
                                            <span class="chip chip-success"><i class="fa fa-check-circle me-1"></i><?php echo htmlentities($result->paymentType); ?></span>
                                        <?php } else { ?>
                                            <span class="chip chip-warn"><i class="fa fa-exclamation-circle me-1"></i>Unpaid</span>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="summary-right text-end">
                                    <div class="summary-price">RS <?php echo htmlentities($booking_price); ?></div>
                                    <div class="summary-date"><i class="fa fa-calendar me-1"></i><?php echo date('M d, Y', strtotime($result->bookingdate)); ?></div>
                                </div>
                            </div>

                            <div class="row g-4">
                                <!-- Customer Information -->
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <h5><i class="fas fa-user-circle me-2"></i>Customer Information</h5>
                                        <div class="mb-2">
                                            <span class="fw-bold text-secondary">Name:</span> 
                                            <span class="text-dark"><?php echo htmlentities($result->Name); ?></span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="fw-bold text-secondary">Email:</span> 
                                            <span class="text-dark"><?php echo htmlentities($result->email); ?></span>
                                        </div>
                                        <div>
                                            <span class="fw-bold text-secondary">Booking Date:</span> 
                                            <span class="text-dark"><?php echo htmlentities($result->bookingdate); ?></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Package Details -->
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <h5><i class="fas fa-tags me-2"></i>Package Details</h5>
                                        <div class="mb-2">
                                            <span class="fw-bold text-secondary">Category:</span> 
                                            <span class="text-dark"><?php echo htmlentities($result->category_name); ?></span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="fw-bold text-secondary">Package Name:</span> 
                                            <span class="text-dark"><?php echo htmlentities($result->PackageName); ?></span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="fw-bold text-secondary">Title:</span> 
                                            <span class="text-dark"><?php echo htmlentities($result->title); ?></span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="fw-bold text-secondary">Duration:</span> 
                                            <span class="text-dark"><?php echo htmlentities($result->PackageDuratiobn); ?></span>
                                        </div>
                                        <div>
                                            <span class="fw-bold text-secondary">Price:</span> 
                                            <span class="text-success fw-bold fs-5">RS <?php echo htmlentities($result->Price); ?></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Description -->
                                <div class="col-12">
                                    <div class="info-card">
                                        <h5><i class="fas fa-align-left me-2"></i>Description & Terms</h5>
                                        <p class="text-dark mb-0" style="line-height: 1.8;"><?php echo nl2br(htmlentities($result->Description)); ?></p>
                                    </div>
                                </div>
                                
                                <!-- Payment Status & Action -->
                                <div class="col-12">
                                    <div class="info-card">
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <h5><i class="fas fa-money-bill-wave me-2"></i>Payment Status</h5>
                                                <div>
                                                    <?php
                                                    $ptype = isset($result->paymentType) ? $result->paymentType : '';
                                                    if($ptype) {
                                                        echo '<span class="badge bg-success fs-6 p-2">' . htmlentities($ptype) . '</span>';
                                                    } else {
                                                        echo '<span class="badge bg-warning text-dark fs-6 p-2">Payment not made yet</span>';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                                                <?php if(!$ptype): ?>
                                                    <div class="d-flex gap-2 justify-content-md-end flex-wrap">
                                                        <button id="payment-button" class="btn-payment">
                                                            <i class="fas fa-credit-card me-2"></i>
                                                            Payment through Khalti
                                                        </button>
                                                        <button id="cash-pending-button" class="btn btn-outline-secondary" style="border-radius:28px; font-weight:800;">
                                                            <i class="fas fa-money-bill-wave me-2"></i>
                                                            Pay Cash (Pending)
                                                        </button>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                                    }
                                } else {
                                    echo '<div class="alert alert-danger text-center">No booking detail found for this booking.</div>';
                                }
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Payment History Table -->
                    <?php if(!$hasError && isset($bookindid) && $bookindid > 0) {
                        $sql2 = "SELECT * FROM tblpayment WHERE bookingID = :bookindid";
                        $query2 = $dbh->prepare($sql2);
                        $query2->bindParam(':bookindid', $bookindid, PDO::PARAM_INT);
                        $query2->execute();
                        $results2 = $query2->fetchAll(PDO::FETCH_OBJ);
                        $gpayment = 0;
                        if ($query2->rowCount() > 0) {
                    ?>
                    <div class="detail-card" data-aos="fade-up" data-aos-delay="200" style="margin-top: 30px;">
                        <div class="detail-card-header">
                            <h4><i class="fas fa-credit-card me-2"></i>Payment History</h4>
                        </div>
                        <div class="detail-card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Payment Type</th>
                                            <th>Amount Paid</th>
                                            <th>Payment Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($results2 as $result2) { ?>
                                        <tr>
                                            <td><?php echo htmlentities($result2->paymentType); ?></td>
                                            <td>RS <?php echo htmlentities($result2->payment); $gpayment += $result2->payment; ?></td>
                                            <td><?php echo htmlentities($result2->payment_date); ?></td>
                                        </tr>
                                        <?php } ?>
                                        <tr class="table-light fw-bold">
                                            <td class="text-end">Total</td>
                                            <td class="text-success">RS <?php echo htmlentities($gpayment); ?></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php } } ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 20px; overflow: hidden;">
                <div class="modal-header" style="background: var(--gradient-primary); color: white; border: none;">
                    <h5 class="modal-title"><i class="fas fa-credit-card me-2"></i>Make Payment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <?php if(!$hasError && isset($booking_title)): ?>
                    <div class="payment-info mb-4">
                        <h6 class="text-secondary mb-3">Booking Details</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Package:</span>
                            <strong><?php echo htmlentities($booking_title); ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Duration:</span>
                            <strong><?php echo htmlentities($booking_duration); ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span>Amount:</span>
                            <strong class="text-success fs-5">RS <?php echo htmlentities($booking_price); ?></strong>
                        </div>
                    </div>
                    
                    <form id="paymentForm">
                        <input type="hidden" id="booking_id" value="<?php echo $bookindid; ?>">
                        <input type="hidden" id="amount" value="<?php echo $booking_price; ?>">
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
							<select class="form-select" id="payment_method" required>
								<option value="">Select payment method</option>
								<option value="Cash">Cash</option>
								<option value="Khalti">Khalti</option>
							</select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" style="background: var(--gradient-primary); border: none;">
                                <i class="fas fa-check me-2"></i>Confirm Payment
                            </button>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="js/vendor/jquery-3.2.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://khalti.com/static/khalti-checkout.js"></script>
    
    <script>
        // Initialize AOS animations
        AOS.init({
            duration: 1000,
            easing: 'ease-in-out',
            once: true
        });
        
        // Payment Modal Functions
        let paymentModal;
        
        function showPaymentModal() {
            paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
            paymentModal.show();
        }
        
        // Handle payment form submission (non-Khalti)
        document.addEventListener('DOMContentLoaded', function() {
            // Khalti integration
            const publicKey = '<?php echo htmlspecialchars($KHALTI_PK, ENT_QUOTES); ?>';
            // Consider configured if key looks like a real test/live key and not a placeholder
            const isConfigured = !!publicKey && publicKey !== 'PUT_YOUR_PUBLIC_KEY_HERE' && (/^(test_|live_)/.test(publicKey) || publicKey.length >= 20);
            
            function launchKhalti() {
                const bookingId = document.getElementById('booking_id').value;
                const amountNpr = parseInt(document.getElementById('amount').value, 10);
                if (!Number.isFinite(amountNpr) || amountNpr <= 0) {
                    alert('Invalid amount configured for this booking.');
                    return;
                }
                const productName = '<?php echo isset($booking_title) ? htmlspecialchars($booking_title, ENT_QUOTES) : 'Gym Package'; ?>';
                
                const checkout = new KhaltiCheckout({
                    publicKey: publicKey,
                    productIdentity: String(bookingId),
                    productName: productName,
                    productUrl: window.location.href,
                    eventHandler: {
                        onSuccess: function(payload) {
                            fetch('khalti/verify.php', {
                                method: 'POST', 
                                headers: {'Content-Type':'application/json'},
                                body: JSON.stringify({ 
                                    token: payload.token, 
                                    amount: payload.amount, 
                                    booking_id: bookingId 
                                })
                            })
                            .then(async (r) => {
                                let data;
                                try { data = await r.json(); } catch (e) { data = { success:false, message: 'Invalid server response' }; }
                                if (!r.ok) {
                                    const msg = (data && data.message) ? data.message : ('HTTP ' + r.status);
                                    throw new Error(msg);
                                }
                                return data;
                            })
                            .then(res => {
                                if (res.success) { 
                                    alert('Payment successful!'); 
                                    window.location.reload(); 
                                } else { 
                                    alert(res.message || 'Verification failed'); 
                                }
                            })
                            .catch((err) => {
                                console.error('Khalti verification error:', err);
                                alert('Payment verification failed: ' + (err && err.message ? err.message : 'Unknown error'));
                            });
                        },
                        onError: function(err){ 
                            console.error('Khalti onError:', err); 
                            const msg = (err && err.message) ? err.message : 'Payment error.';
                            alert(msg); 
                        },
                        onClose: function(){}
                    }
                });
                checkout.show({ amount: amountNpr * 100 });
            }
            
            const payBtn = document.getElementById('payWithKhalti');
            if (payBtn) {
                payBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (!isConfigured) { 
                        alert('Khalti is not configured.'); 
                        return; 
                    }
                    launchKhalti();
                });
            }
            
            // One-click from main Make Payment button if configured
            const makeBtn = document.getElementById('payment-button');
            if (makeBtn) {
                if (isConfigured) {
                    // Direct Khalti payment
                    makeBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (typeof KhaltiCheckout === 'undefined') { 
                            alert('Khalti script failed to load.'); 
                            return; 
                        }
                        launchKhalti();
                    });
                } else {
                    // Show modal for other payment methods
                    makeBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        showPaymentModal();
                    });
                }
            }

            // Cash pending button
            const cashBtn = document.getElementById('cash-pending-button');
            if (cashBtn) {
                cashBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const bookingIdEl = document.getElementById('booking_id');
                    const bookingId = bookingIdEl ? bookingIdEl.value : '<?php echo isset($bookindid) ? (int)$bookindid : 0; ?>';
                    if (!bookingId || parseInt(bookingId,10) <= 0) { alert('Invalid booking.'); return; }

                    const original = cashBtn.innerHTML;
                    cashBtn.disabled = true;
                    cashBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

                    fetch('cash-payment.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ booking_id: bookingId })
                    })
                    .then(async (r) => {
                        let data; try { data = await r.json(); } catch(e){ data = null; }
                        if (!r.ok) { throw new Error((data && data.message) ? data.message : ('HTTP ' + r.status)); }
                        return data;
                    })
                    .then((res) => {
                        alert(res.message || 'Marked cash as pending.');
                        window.location.reload();
                    })
                    .catch((err) => {
                        alert('Failed to mark cash pending: ' + (err && err.message ? err.message : 'Unknown error'));
                        cashBtn.disabled = false;
                        cashBtn.innerHTML = original;
                    });
                });
            }
            
            const paymentForm = document.getElementById('paymentForm');
            if(paymentForm) {
                paymentForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const bookingId = document.getElementById('booking_id').value;
                    const amount = document.getElementById('amount').value;
                    const paymentMethod = document.getElementById('payment_method').value;
                    
                    if(!paymentMethod) { 
                        alert('Please select a payment method'); 
                        return; 
                    }
                    
                    // If Khalti selected, use gateway instead of manual
                    if (paymentMethod === 'Khalti') {
                        if (!isConfigured) { alert('Khalti is not configured.'); return; }
                        launchKhalti();
                        return;
                    }
                    
                    const submitBtn = paymentForm.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
                    
                    fetch('process-payment.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ 
                            booking_id: bookingId, 
                            amount: amount, 
                            payment_method: paymentMethod 
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            alert('Payment recorded successfully!');
                            if(paymentModal) paymentModal.hide();
                            window.location.reload();
                        } else {
                            alert('Error: ' + (data.message || 'Unknown error occurred'));
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });
                });
            }
        });
    </script>
</body>
</html>
<?php } ?>