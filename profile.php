<?php
session_start();
error_reporting(1); // Enable error reporting for debugging
require_once('include/config.php');

// Debug: Check if session is working
if(!isset($_SESSION['uid']) || strlen($_SESSION['uid'])==0) {
header('location:login.php');
    exit();
}

$uid = $_SESSION['uid'];

// Debug: Show session info
// echo "Session UID: " . $uid . "<br>";

if(isset($_POST['submit'])) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $address = $_POST['address'];
    
    $sql = "UPDATE tbluser SET fname=:fname, lname=:lname, mobile=:mobile, city=:city, state=:state, address=:Address WHERE id=:uid";
$query = $dbh->prepare($sql);
    $query->bindParam(':fname', $fname, PDO::PARAM_STR);
    $query->bindParam(':lname', $lname, PDO::PARAM_STR);
    $query->bindParam(':mobile', $mobile, PDO::PARAM_STR);
    $query->bindParam(':city', $city, PDO::PARAM_STR);
    $query->bindParam(':state', $state, PDO::PARAM_STR);
    $query->bindParam(':Address', $address, PDO::PARAM_STR);
    $query->bindParam(':uid', $uid, PDO::PARAM_STR);
    
    if($query->execute()) {
        echo "<script>alert('Profile has been updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating profile. Please try again.');</script>";
    }
}
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>My Profile | Elite Fitness</title>
	<meta charset="UTF-8">
	<meta name="description" content="Manage your Elite Fitness profile - Update personal information and preferences">
	<meta name="keywords" content="profile, account, settings, fitness, gym">
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
			padding: 80px 0;
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
			font-size: 3.5rem;
			font-weight: 800;
			margin-bottom: 1.5rem;
			text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
		}
		
		.page-subtitle {
			font-size: 1.3rem;
			opacity: 0.9;
			max-width: 700px;
			margin: 0 auto;
		}
		
		/* Profile Section */
		.profile-section {
			padding: 80px 0;
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
			max-width: 600px;
			margin: 0 auto;
		}
		
		/* Profile Form Container */
		.profile-form-container {
			background: white;
			border-radius: 25px;
			padding: 3rem;
			box-shadow: 0 15px 50px rgba(0,0,0,0.1);
			border: 1px solid rgba(0,0,0,0.05);
			position: relative;
			overflow: hidden;
			max-width: 800px;
			margin: 0 auto;
		}
		
		.profile-form-container::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			height: 5px;
			background: var(--gradient-primary);
		}
		
		.form-title {
			font-size: 2rem;
			margin-bottom: 2rem;
			text-align: center;
			color: var(--text-primary);
		}
		
		/* Form Styling */
		.form-group {
			margin-bottom: 1.5rem;
		}
		
		.form-label {
			display: block;
			margin-bottom: 0.5rem;
			font-weight: 500;
			color: var(--text-primary);
			font-size: 0.95rem;
		}
		
		.form-control {
			width: 100%;
			padding: 15px 20px;
			border: 2px solid #e5e7eb;
			border-radius: 15px;
			font-size: 1rem;
			transition: all 0.3s ease;
			background: var(--light-color);
			color: var(--text-primary);
		}
		
		.form-control:focus {
			outline: none;
			border-color: var(--primary-color);
			box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
			background: white;
		}
		
		.form-control:read-only {
			background: #f3f4f6;
			color: var(--text-secondary);
			cursor: not-allowed;
		}
		
		.form-control::placeholder {
			color: var(--text-secondary);
			opacity: 0.7;
		}
		
		/* Profile Info Display */
		.profile-info {
			background: var(--light-color);
			border-radius: 20px;
			padding: 2rem;
			margin-bottom: 2rem;
			text-align: center;
			border: 2px solid rgba(99, 102, 241, 0.1);
		}
		
		.profile-avatar {
			width: 100px;
			height: 100px;
			background: var(--gradient-accent);
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			margin: 0 auto 1rem;
			font-size: 2.5rem;
			color: white;
		}
		
		.profile-name {
			font-size: 1.5rem;
			font-weight: 700;
			color: var(--text-primary);
			margin-bottom: 0.5rem;
		}
		
		.profile-email {
			color: var(--text-secondary);
			font-size: 1rem;
		}
		
		/* Submit Button */
		.btn-update {
			background: var(--gradient-secondary);
			border: none;
			color: white;
			padding: 15px 40px;
			border-radius: 25px;
			font-weight: 600;
			font-size: 1.1rem;
			cursor: pointer;
			transition: all 0.3s ease;
			width: 100%;
			margin-top: 1rem;
		}
		
		.btn-update:hover {
			transform: translateY(-2px);
			box-shadow: 0 10px 30px rgba(240, 147, 251, 0.4);
		}
		
		.btn-update:active {
			transform: translateY(0);
		}
		
		/* Form Row Styling */
		.form-row {
			display: grid;
			grid-template-columns: 1fr 1fr;
			gap: 1.5rem;
		}
		
		.form-row.full-width {
			grid-template-columns: 1fr;
		}
		
		/* Error/No Data Display */
		.no-data-message {
			text-align: center;
			padding: 3rem 2rem;
			color: var(--text-secondary);
		}
		
		.no-data-message i {
			font-size: 4rem;
			color: var(--text-secondary);
			margin-bottom: 1rem;
			opacity: 0.5;
		}
		
		.no-data-message h3 {
			color: var(--text-primary);
			margin-bottom: 1rem;
		}
		
		.no-data-message p {
			margin-bottom: 2rem;
		}
		
		.btn-login {
			background: var(--gradient-primary);
			color: white;
			text-decoration: none;
			padding: 12px 30px;
			border-radius: 25px;
			font-weight: 600;
			display: inline-block;
			transition: all 0.3s ease;
		}
		
		.btn-login:hover {
			color: white;
			transform: translateY(-2px);
			box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
		}
		
		/* Responsive Design */
		@media (max-width: 768px) {
			.page-title {
				font-size: 2.5rem;
			}
			
			.section-title {
				font-size: 2rem;
			}
			
			.profile-form-container {
				padding: 2rem;
				margin: 0 -15px;
				border-radius: 15px;
			}
			
			.form-row {
				grid-template-columns: 1fr;
				gap: 1rem;
			}
			
			.form-control {
				padding: 12px 16px;
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

	<!-- Page Header -->
	<section class="page-header">
		<div class="container">
			<div class="page-header-content" data-aos="fade-up">
				<h1 class="page-title">My Profile</h1>
				<p class="page-subtitle">Manage your personal information and keep your Elite Fitness account up to date</p>
			</div>
		</div>
	</section>

	<!-- Profile Section -->
	<section class="profile-section">
		<div class="container">
			<div class="section-header" data-aos="fade-up">
				<h2 class="section-title">Profile Information</h2>
				<p class="section-subtitle">Update your personal details and contact information</p>
			</div>
			
			<div class="profile-form-container" data-aos="fade-up" data-aos-delay="200">
				<?php 
				// Debug: Show current user ID
				// echo "<!-- Debug: Current UID: " . $uid . " -->";
				
				$sql = "SELECT id, fname, lname, email, mobile, password, address, state, city, create_date FROM tbluser WHERE id = :uid";
				$query = $dbh->prepare($sql);
				$query->bindParam(':uid', $uid, PDO::PARAM_STR);
				$query->execute();
				$results = $query->fetchAll(PDO::FETCH_OBJ);
				
				// Debug: Show query results
				// echo "<!-- Debug: Query executed, row count: " . $query->rowCount() . " -->";
				
				if($query->rowCount() > 0) {
					foreach($results as $result) {
				?>
				
				<!-- Profile Info Display -->
				<div class="profile-info">
					<div class="profile-avatar">
						<i class="fas fa-user"></i>
					</div>
					<div class="profile-name"><?php echo htmlentities($result->fname . ' ' . $result->lname); ?></div>
					<div class="profile-email"><?php echo htmlentities($result->email); ?></div>
				</div>
				
				<form method="post" class="profile-form">
					<div class="form-row">
						<div class="form-group">
							<label class="form-label">First Name</label>
							<input type="text" name="fname" id="fname" class="form-control" placeholder="Enter your first name" autocomplete="off" value="<?php echo htmlentities($result->fname); ?>" required>
							</div>
						<div class="form-group">
							<label class="form-label">Last Name</label>
							<input type="text" name="lname" id="lname" class="form-control" placeholder="Enter your last name" autocomplete="off" value="<?php echo htmlentities($result->lname); ?>" required>
							</div>
							</div>
					
					<div class="form-row">
						<div class="form-group">
							<label class="form-label">Email Address</label>
							<input type="email" name="email" id="email" class="form-control" placeholder="Your email address" autocomplete="off" value="<?php echo htmlentities($result->email); ?>" readonly>
							</div>
						<div class="form-group">
							<label class="form-label">Mobile Number</label>
							<input type="tel" name="mobile" id="mobile" class="form-control" placeholder="Enter your mobile number" autocomplete="off" value="<?php echo htmlentities($result->mobile); ?>" required>
							</div>
							</div>
							
					<div class="form-row">
						<div class="form-group">
							<label class="form-label">State</label>
							<input type="text" name="state" id="state" class="form-control" placeholder="Enter your state" autocomplete="off" value="<?php echo htmlentities($result->state); ?>" required>
						</div>
						<div class="form-group">
							<label class="form-label">City</label>
							<input type="text" name="city" id="city" class="form-control" placeholder="Enter your city" autocomplete="off" value="<?php echo htmlentities($result->city); ?>" required>
						</div>
							</div>
								
					<div class="form-row full-width">
						<div class="form-group">
							<label class="form-label">Address</label>
							<input type="text" name="address" id="address" class="form-control" placeholder="Enter your complete address" autocomplete="off" value="<?php echo htmlentities($result->address); ?>" required>
							</div>
						</div>
					
					<button type="submit" id="submit" name="submit" class="btn-update">
						<i class="fas fa-save me-2"></i>Update Profile
					</button>
					</form>
				
				<?php 
					}
				} else {
					// Show message when no user data is found
				?>
				
				<div class="no-data-message">
					<i class="fas fa-user-slash"></i>
					<h3>No Profile Data Found</h3>
					<p>We couldn't find your profile information. This might happen if you're not properly logged in or if there's an issue with your account.</p>
					<a href="login.php" class="btn-login">
						<i class="fas fa-sign-in-alt me-2"></i>Go to Login
					</a>
				</div>
				
				<?php 
				} 
				?>
			</div>
		</div>
	</section>

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
		
		// Form validation and enhancement
		document.addEventListener('DOMContentLoaded', function() {
			const form = document.querySelector('.profile-form');
			const inputs = document.querySelectorAll('.form-control');
			
			// Add focus effects
			inputs.forEach(input => {
				input.addEventListener('focus', function() {
					this.parentElement.style.transform = 'scale(1.02)';
					this.parentElement.style.transition = 'transform 0.2s ease';
				});
				
				input.addEventListener('blur', function() {
					this.parentElement.style.transform = 'scale(1)';
				});
			});
			
			// Form submission enhancement
			if (form) {
				form.addEventListener('submit', function(e) {
					const requiredFields = form.querySelectorAll('[required]');
					let isValid = true;
					
					requiredFields.forEach(field => {
						if (!field.value.trim()) {
							isValid = false;
							field.style.borderColor = '#ef4444';
						} else {
							field.style.borderColor = '#e5e7eb';
						}
					});
					
					if (!isValid) {
						e.preventDefault();
						alert('Please fill in all required fields.');
					}
				});
			}
		});
	</script>
	</body>
</html>
