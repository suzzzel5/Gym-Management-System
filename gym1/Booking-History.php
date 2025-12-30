<?php session_start();
error_reporting(0);
require_once('include/config.php');

if(strlen( $_SESSION["uid"])==0) {   
header('location:login.php');
    exit();
}

$uid = $_SESSION['uid'];
$msg = "";
$error = "";

// Check for booking success message from session
if(isset($_SESSION['booking_success'])) {
    $msg = $_SESSION['booking_success'];
    unset($_SESSION['booking_success']);
}

// Handle delete booking
if(isset($_POST['delete_booking'])) {
    $booking_id = $_POST['booking_id'];
    
    // Verify the booking belongs to the current user
    $verify_sql = "SELECT id FROM tblbooking WHERE id=:booking_id AND userid=:uid";
    $verify_query = $dbh->prepare($verify_sql);
    $verify_query->bindParam(':booking_id', $booking_id, PDO::PARAM_STR);
    $verify_query->bindParam(':uid', $uid, PDO::PARAM_STR);
    $verify_query->execute();
    
    if($verify_query->rowCount() > 0) {
        // Delete the booking
        $delete_sql = "DELETE FROM tblbooking WHERE id=:booking_id AND userid=:uid";
        $delete_query = $dbh->prepare($delete_sql);
        $delete_query->bindParam(':booking_id', $booking_id, PDO::PARAM_STR);
        $delete_query->bindParam(':uid', $uid, PDO::PARAM_STR);
        
        if($delete_query->execute()) {
            $msg = "Booking deleted successfully!";
        } else {
            $error = "Error deleting booking. Please try again.";
        }
    } else {
        $error = "Invalid booking or unauthorized access.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>My Bookings | Elite Fitness</title>
	<meta charset="UTF-8">
	<meta name="description" content="View your fitness package bookings and track your fitness journey">
	<meta name="keywords" content="bookings, fitness, gym, packages">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<!-- Google Fonts -->
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	
	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	
	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	
	<!-- AOS Animation -->
	<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
	
	<!-- Custom CSS -->
	<style>
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
			--gradient-accent: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
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
		
		.section-padding {
			padding: 80px 0;
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
		
		.page-header-content {
			position: relative;
			z-index: 2;
		}
		
		.page-title {
			font-size: 3rem;
			font-weight: 800;
			margin-bottom: 1rem;
			text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
		}
		
		.page-subtitle {
			font-size: 1.2rem;
			opacity: 0.9;
			max-width: 600px;
			margin: 0 auto;
		}
		
		/* Booking Section */
		.booking-section {
			padding: 60px 0;
			background: white;
		}
		
		.section-header {
			text-align: center;
			margin-bottom: 3rem;
		}
		
		.section-title {
			font-size: 2.5rem;
			margin-bottom: 1rem;
			background: var(--gradient-primary);
			-webkit-background-clip: text;
			-webkit-text-fill-color: transparent;
			background-clip: text;
		}
		
		.section-subtitle {
			color: var(--text-secondary);
			font-size: 1.1rem;
		}
		
		/* Table Styling */
		.booking-table-container {
			background: white;
			border-radius: 20px;
			box-shadow: 0 10px 40px rgba(0,0,0,0.1);
			overflow: hidden;
			border: 1px solid rgba(0,0,0,0.05);
		}
		
		.booking-table {
			margin: 0;
			width: 100%;
		}
		
		.booking-table thead th {
			background: var(--gradient-primary);
			color: white;
			font-weight: 600;
			padding: 20px 15px;
			border: none;
			font-size: 0.95rem;
			text-transform: uppercase;
			letter-spacing: 0.5px;
		}
		
		.booking-table tbody td {
			padding: 20px 15px;
			border-bottom: 1px solid #f0f0f0;
			vertical-align: middle;
			font-size: 0.95rem;
		}
		
		.booking-table tbody tr:last-child td {
			border-bottom: none;
		}
		
		.booking-table tbody tr:hover {
			background: rgba(99, 102, 241, 0.05);
		}
		
		/* Package Info */
		.package-title {
			font-weight: 600;
			color: var(--primary-color);
			margin-bottom: 5px;
		}
		
		.package-category {
			font-size: 0.85rem;
			color: var(--text-secondary);
			background: rgba(99, 102, 241, 0.1);
			padding: 4px 12px;
			border-radius: 15px;
			display: inline-block;
		}
		
		.package-duration {
			font-weight: 500;
			color: var(--text-primary);
		}
		
		.package-price {
			font-weight: 700;
			color: var(--secondary-color);
			font-size: 1.1rem;
		}
		
		.package-description {
			color: var(--text-secondary);
			font-size: 0.9rem;
			line-height: 1.5;
			max-width: 200px;
		}
		
		.booking-date {
			font-weight: 500;
			color: var(--text-primary);
		}
		
		/* Action Buttons */
		.action-buttons {
			display: flex;
			gap: 10px;
			flex-wrap: wrap;
		}
		
		.btn-view {
			background: var(--gradient-primary);
			border: none;
			color: white;
			padding: 8px 20px;
			border-radius: 20px;
			font-weight: 500;
			text-decoration: none;
			display: inline-block;
			transition: all 0.3s ease;
			font-size: 0.9rem;
		}
		
		.btn-view:hover {
			color: white;
			transform: translateY(-2px);
			box-shadow: 0 5px 20px rgba(99, 102, 241, 0.4);
		}
		
		.btn-delete {
			background: var(--gradient-accent);
			border: none;
			color: white;
			padding: 8px 20px;
			border-radius: 20px;
			font-weight: 500;
			text-decoration: none;
			display: inline-block;
			transition: all 0.3s ease;
			font-size: 0.9rem;
			cursor: pointer;
		}
		
		.btn-delete:hover {
			color: white;
			transform: translateY(-2px);
			box-shadow: 0 5px 20px rgba(79, 172, 254, 0.4);
		}
		
		/* Delete Confirmation Modal */
		.delete-modal {
			display: none;
			position: fixed;
			z-index: 1000;
			left: 0;
			top: 0;
			width: 100%;
			height: 100%;
			background-color: rgba(0,0,0,0.5);
		}
		
		.delete-modal-content {
			background-color: white;
			margin: 15% auto;
			padding: 30px;
			border-radius: 20px;
			width: 90%;
			max-width: 500px;
			text-align: center;
			box-shadow: 0 20px 60px rgba(0,0,0,0.3);
		}
		
		.delete-modal-icon {
			font-size: 4rem;
			color: var(--accent-color);
			margin-bottom: 1rem;
		}
		
		.delete-modal-title {
			font-size: 1.5rem;
			font-weight: 700;
			margin-bottom: 1rem;
			color: var(--text-primary);
		}
		
		.delete-modal-message {
			color: var(--text-secondary);
			margin-bottom: 2rem;
			line-height: 1.6;
		}
		
		.delete-modal-buttons {
			display: flex;
			gap: 15px;
			justify-content: center;
			flex-wrap: wrap;
		}
		
		.btn-cancel {
			background: var(--text-secondary);
			border: none;
			color: white;
			padding: 12px 25px;
			border-radius: 25px;
			font-weight: 600;
			cursor: pointer;
			transition: all 0.3s ease;
		}
		
		.btn-cancel:hover {
			background: var(--dark-color);
			transform: translateY(-2px);
		}
		
		.btn-confirm-delete {
			background: var(--accent-color);
			border: none;
			color: white;
			padding: 12px 25px;
			border-radius: 25px;
			font-weight: 600;
			cursor: pointer;
			transition: all 0.3s ease;
		}
		
		.btn-confirm-delete:hover {
			background: #dc2626;
			transform: translateY(-2px);
			box-shadow: 0 5px 20px rgba(239, 68, 68, 0.4);
		}
		
		/* Empty State */
		.empty-state {
			text-align: center;
			padding: 60px 20px;
			color: var(--text-secondary);
		}
		
		.empty-state i {
			font-size: 4rem;
			color: var(--text-secondary);
			margin-bottom: 1rem;
			opacity: 0.5;
		}
		
		.empty-state h3 {
			color: var(--text-primary);
			margin-bottom: 1rem;
		}
		
		.empty-state p {
			margin-bottom: 2rem;
		}
		
		.btn-explore {
			background: var(--gradient-secondary);
			color: white;
			text-decoration: none;
			padding: 12px 30px;
			border-radius: 25px;
			font-weight: 600;
			display: inline-block;
			transition: all 0.3s ease;
		}
		
		.btn-explore:hover {
			color: white;
			transform: translateY(-2px);
			box-shadow: 0 8px 25px rgba(240, 147, 251, 0.4);
		}
		
		/* Responsive Table */
		@media (max-width: 768px) {
			.page-title {
				font-size: 2rem;
			}
			
			.section-title {
				font-size: 2rem;
			}
			
			.booking-table-container {
				border-radius: 15px;
				margin: 0 -15px;
			}
			
			.booking-table thead {
				display: none;
			}
			
			.booking-table tbody tr {
				display: block;
				margin-bottom: 20px;
				border: 1px solid #f0f0f0;
				border-radius: 15px;
				padding: 20px;
				background: white;
			}
			
			.booking-table tbody td {
				display: block;
				border: none;
				padding: 8px 0;
				text-align: left;
			}
			
			.booking-table tbody td:before {
				content: attr(data-label) ": ";
				font-weight: 600;
				color: var(--primary-color);
				display: inline-block;
				width: 120px;
			}
			
			.package-description {
				max-width: none;
			}
			
			.action-buttons {
				justify-content: flex-start;
			}
		}
		
		/* Success/Error Messages */
		.errorWrap {
			padding: 15px 20px;
			margin: 0 0 20px 0;
			background: var(--accent-color);
			color: white;
			border-radius: 10px;
			box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
			font-weight: 500;
		}
		
		.succWrap {
			padding: 15px 20px;
			margin: 0 0 20px 0;
			background: #10b981;
			color: white;
			border-radius: 10px;
			box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
			font-weight: 500;
		}
	</style>
</head>
<body>
	<!-- Header Section -->
	<?php include 'include/header.php';?>

	<!-- Booking Section -->
	<section class="booking-section">
		<div class="container">
			<div class="section-header" data-aos="fade-up">
				<h2 class="section-title">Booking History</h2>
				<p class="section-subtitle">View all your past and current fitness package bookings</p>
			</div>
			
			<!-- Success/Error Messages -->
			<?php if($error){ ?>
				<div class="errorWrap" data-aos="fade-up">
					<i class="fas fa-exclamation-triangle me-2"></i>
					<strong>Error:</strong> <?php echo htmlentities($error); ?>
				</div>
			<?php } else if($msg){ ?>
				<div class="succWrap" data-aos="fade-up">
					<i class="fas fa-check-circle me-2"></i>
					<strong>Success:</strong> <?php echo htmlentities($msg); ?>
				</div>
			<?php } ?>
			
			<div class="row">
				<div class="col-lg-12">
					<?php
					$uid = $_SESSION['uid'];
					// Convert uid to string for proper matching with VARCHAR column
					$uid = (string)$uid;
					
					// Check if status column exists
					$hasStatusColumn = false;
					try {
						$checkColSql = "SHOW COLUMNS FROM tblbooking LIKE 'status'";
						$checkColQuery = $dbh->query($checkColSql);
						$hasStatusColumn = ($checkColQuery->rowCount() > 0);
					} catch (PDOException $e) {
						$hasStatusColumn = false;
					}
					
					// Build query based on whether status column exists
					if($hasStatusColumn) {
						$sql = "SELECT t1.id as bookingid, COALESCE(t1.status, 'pending') as status, 
								t3.fname as Name, t3.email as email, t1.booking_date as bookingdate, 
								t2.titlename as title, t2.PackageDuratiobn as PackageDuratiobn,
								t2.Price as Price, t2.Description as Description, 
								t4.category_name as category_name, t5.PackageName as PackageName 
								FROM tblbooking as t1
								LEFT JOIN tbladdpackage as t2 ON t1.package_id = t2.id
								LEFT JOIN tbluser as t3 ON t1.userid = t3.id
								LEFT JOIN tblcategory as t4 ON t2.category = t4.id
								LEFT JOIN tblpackage as t5 ON t2.PackageType = t5.id
								WHERE t1.userid = :uid 
								ORDER BY t1.booking_date DESC";
					} else {
						$sql = "SELECT t1.id as bookingid, 'pending' as status, 
								t3.fname as Name, t3.email as email, t1.booking_date as bookingdate, 
								t2.titlename as title, t2.PackageDuratiobn as PackageDuratiobn,
								t2.Price as Price, t2.Description as Description, 
								t4.category_name as category_name, t5.PackageName as PackageName 
								FROM tblbooking as t1
								LEFT JOIN tbladdpackage as t2 ON t1.package_id = t2.id
								LEFT JOIN tbluser as t3 ON t1.userid = t3.id
								LEFT JOIN tblcategory as t4 ON t2.category = t4.id
								LEFT JOIN tblpackage as t5 ON t2.PackageType = t5.id
								WHERE t1.userid = :uid 
								ORDER BY t1.booking_date DESC";
					}
					
					$results = [];
					$cnt = 1;
					
					try {
						$query = $dbh->prepare($sql);
						$query->bindParam(':uid', $uid, PDO::PARAM_STR);
						$query->execute();
						$results = $query->fetchAll(PDO::FETCH_OBJ);
						
						// Debug: If no results, check if bookings exist at all
						if(isset($_GET['debug']) && count($results) == 0) {
							$testSql = "SELECT t1.id, t1.package_id, t1.userid, t2.id as package_exists, t2.titlename 
										FROM tblbooking as t1 
										LEFT JOIN tbladdpackage as t2 ON t1.package_id = t2.id 
										WHERE t1.userid = :uid LIMIT 5";
							$testQuery = $dbh->prepare($testSql);
							$testQuery->bindParam(':uid', $uid, PDO::PARAM_STR);
							$testQuery->execute();
							$testResults = $testQuery->fetchAll(PDO::FETCH_OBJ);
							echo "<div class='alert alert-warning'>Debug: Found " . count($testResults) . " raw booking(s). ";
							if(count($testResults) > 0) {
								echo "First booking: ID=" . $testResults[0]->id . ", package_id=" . $testResults[0]->package_id . ", package_exists=" . ($testResults[0]->package_exists ? 'Yes' : 'No');
							}
							echo "</div>";
						}
					} catch (PDOException $e) {
						// Log the error for debugging
						error_log("Booking History Query Error: " . $e->getMessage());
						$error = "Error loading bookings. Please try again later.";
						if(isset($_GET['debug'])) {
							$error .= " Error: " . $e->getMessage() . " | SQL: " . $sql;
						}
					}
					
					// Debug: Check if bookings exist for this user (add ?debug=1 to URL to see)
					if(isset($_GET['debug'])) {
						$debugSql = "SELECT COUNT(*) as total FROM tblbooking WHERE userid = :uid";
						$debugQuery = $dbh->prepare($debugSql);
						$debugQuery->bindParam(':uid', $uid, PDO::PARAM_STR);
						$debugQuery->execute();
						$debugResult = $debugQuery->fetch(PDO::FETCH_OBJ);
						echo "<div class='alert alert-info'>Debug: Found " . ($debugResult->total ?? 0) . " booking(s) for user ID: " . htmlentities($uid) . " | Results count: " . count($results) . "</div>";
					}
					
					// Check if we have results
					if(count($results) > 0) {
					?>
					
					<div class="booking-table-container" data-aos="fade-up">
						<table class="booking-table">
    <thead>
      <tr>
									<th>#</th>
									<th>Package Details</th>
									<th>Category</th>
									<th>Duration</th>
									<th>Price</th>
									<th>Booking Date</th>
									<th>Status</th>
									<th>Actions</th>
      </tr>
    </thead>
                <tbody>
								<?php foreach($results as $result) { ?>
								<tr>
									<td data-label="Sr. No"><?php echo $cnt; ?></td>
									<td data-label="Package Details">
										<div class="package-title"><?php echo htmlentities($result->title ?? 'N/A'); ?></div>
										<?php if(!empty($result->category_name)): ?>
										<div class="package-category"><?php echo htmlentities($result->category_name); ?></div>
										<?php endif; ?>
										<?php if(!empty($result->Description)): ?>
										<div class="package-description"><?php echo htmlentities($result->Description); ?></div>
										<?php endif; ?>
									</td>
									<td data-label="Category">
										<span class="package-category"><?php echo htmlentities($result->category_name ?? 'N/A'); ?></span>
									</td>
									<td data-label="Duration">
										<span class="package-duration"><?php echo htmlentities($result->PackageDuratiobn ?? 'N/A'); ?></span>
									</td>
									<td data-label="Price">
										<span class="package-price">RS <?php echo htmlentities($result->Price ?? '0'); ?></span>
									</td>
									<td data-label="Booking Date">
										<span class="booking-date"><?php 
											if(!empty($result->bookingdate)) {
												echo date('M d, Y', strtotime($result->bookingdate));
											} else {
												echo 'N/A';
											}
										?></span>
									</td>
									<td data-label="Status">
										<?php 
											$status = isset($result->status) ? $result->status : 'pending';
											if($status == 'pending') {
												echo "<span style='background: #f59e0b; color: white; padding: 5px 12px; border-radius: 15px; font-size: 0.85rem; font-weight: 500;'><i class='fas fa-clock me-1'></i>Pending</span>";
											} elseif($status == 'accepted') {
												echo "<span style='background: #10b981; color: white; padding: 5px 12px; border-radius: 15px; font-size: 0.85rem; font-weight: 500;'><i class='fas fa-check-circle me-1'></i>Accepted</span>";
											} elseif($status == 'declined') {
												echo "<span style='background: #ef4444; color: white; padding: 5px 12px; border-radius: 15px; font-size: 0.85rem; font-weight: 500;'><i class='fas fa-times-circle me-1'></i>Declined</span>";
											}
										?>
									</td>
									<td data-label="Actions">
										<div class="action-buttons">
											<a href="booking-details.php?bookingid=<?php echo htmlentities($result->bookingid); ?>" class="btn-view">
												<i class="fas fa-eye me-2"></i>View
											</a>
											<button type="button" class="btn-delete" onclick="showDeleteModal(<?php echo htmlentities($result->bookingid); ?>, '<?php echo htmlentities($result->title); ?>')">
												<i class="fas fa-trash me-2"></i>Delete
											</button>
										</div>
									</td>
                  </tr>
								<?php 
									$cnt++;
								} 
								?>
                </tbody>
  </table>
				</div>
			
					<?php } else { ?>
					
					<div class="empty-state" data-aos="fade-up">
						<i class="fas fa-calendar-times"></i>
						<h3>No Bookings Found</h3>
						<p>You haven't booked any fitness packages yet. Start your fitness journey today!</p>
						<a href="index.php" class="btn-explore">
							<i class="fas fa-dumbbell me-2"></i>Explore Packages
						</a>
					</div>
					
					<?php } ?>
				</div>
			</div>
		</div>
	</section>

	<!-- Delete Confirmation Modal -->
	<div id="deleteModal" class="delete-modal">
		<div class="delete-modal-content">
			<div class="delete-modal-icon">
				<i class="fas fa-exclamation-triangle"></i>
			</div>
			<h3 class="delete-modal-title">Delete Booking</h3>
			<p class="delete-modal-message">Are you sure you want to delete this booking? This action cannot be undone.</p>
			<div class="delete-modal-buttons">
				<button type="button" class="btn-cancel" onclick="hideDeleteModal()">Cancel</button>
				<form method="post" style="display: inline;">
					<input type="hidden" id="deleteBookingId" name="booking_id" value="">
					<button type="submit" name="delete_booking" class="btn-confirm-delete">Delete Booking</button>
				</form>
			</div>
		</div>
	</div>

	<!-- Footer Section -->
<?php include 'include/footer.php'; ?>

	<!-- Scripts -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
	
	<script>
		// Initialize AOS animations
		AOS.init({
			duration: 1000,
			easing: 'ease-in-out',
			once: true
		});
		
		// Delete modal functions
		function showDeleteModal(bookingId, packageTitle) {
			document.getElementById('deleteBookingId').value = bookingId;
			document.getElementById('deleteModal').style.display = 'block';
			
			// Update modal message with package title
			const modalMessage = document.querySelector('.delete-modal-message');
			modalMessage.innerHTML = `Are you sure you want to delete your booking for <strong>"${packageTitle}"</strong>? This action cannot be undone.`;
		}
		
		function hideDeleteModal() {
			document.getElementById('deleteModal').style.display = 'none';
		}
		
		// Close modal when clicking outside
		window.onclick = function(event) {
			const modal = document.getElementById('deleteModal');
			if (event.target == modal) {
				modal.style.display = 'none';
			}
		}
		
		// Add hover effect to table rows
		document.addEventListener('DOMContentLoaded', function() {
			const tableRows = document.querySelectorAll('.booking-table tbody tr');
			tableRows.forEach(row => {
				row.addEventListener('mouseenter', function() {
					this.style.transform = 'scale(1.01)';
					this.style.transition = 'transform 0.2s ease';
				});
				
				row.addEventListener('mouseleave', function() {
					this.style.transform = 'scale(1)';
				});
			});
		});
	</script>
	</body>
</html>
