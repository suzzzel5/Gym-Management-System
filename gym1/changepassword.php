<?php
session_start();
error_reporting(0);
require_once('include/config.php');

if(strlen($_SESSION["uid"])==0) {   
header('location:login.php');
    exit();
}

$msg = "";
$error = "";

// Code for change password	
if(isset($_POST['submit'])) {
    $password = md5($_POST['password']);
    $newpassword = md5($_POST['newpassword']);
    $email = $_SESSION['email'];
    
    $sql = "SELECT password FROM tbluser WHERE email=:email AND password=:password";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':password', $password, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
    
    if($query->rowCount() > 0) {
        $con = "UPDATE tbluser SET password=:newpassword WHERE email=:email";
$chngpwd1 = $dbh->prepare($con);
        $chngpwd1->bindParam(':email', $email, PDO::PARAM_STR);
        $chngpwd1->bindParam(':newpassword', $newpassword, PDO::PARAM_STR);
        
        if($chngpwd1->execute()) {
            $msg = "Your password has been successfully changed!";
        } else {
            $error = "Error updating password. Please try again.";
        }
    } else {
        $error = "Your current password is not valid.";
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Change Password | Elite Fitness</title>
	<meta charset="UTF-8">
	<meta name="description" content="Change your Elite Fitness account password securely">
	<meta name="keywords" content="password, security, account, fitness, gym">
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
		
		/* Change Password Section */
		.password-section {
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
		
		/* Password Form Container */
		.password-form-container {
			background: white;
			border-radius: 25px;
			padding: 3rem;
			box-shadow: 0 15px 50px rgba(0,0,0,0.1);
			border: 1px solid rgba(0,0,0,0.05);
			position: relative;
			overflow: hidden;
			max-width: 600px;
			margin: 0 auto;
		}
		
		.password-form-container::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			height: 5px;
			background: var(--gradient-secondary);
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
		
		.form-control::placeholder {
			color: var(--text-secondary);
			opacity: 0.7;
		}
		
		/* Password Strength Indicator */
		.password-strength {
			margin-top: 0.5rem;
			font-size: 0.85rem;
		}
		
		.strength-weak { color: var(--accent-color); }
		.strength-medium { color: var(--secondary-color); }
		.strength-strong { color: #10b981; }
		
		/* Submit Button */
		.btn-submit {
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
		
		.btn-submit:hover {
			transform: translateY(-2px);
			box-shadow: 0 10px 30px rgba(240, 147, 251, 0.4);
		}
		
		.btn-submit:active {
			transform: translateY(0);
		}
		
		/* Security Icon */
		.security-icon {
			text-align: center;
			margin-bottom: 2rem;
		}
		
		.security-icon i {
			font-size: 4rem;
			color: var(--primary-color);
			opacity: 0.8;
		}
		
		/* Responsive Design */
		@media (max-width: 768px) {
			.page-title {
				font-size: 2.5rem;
			}
			
			.section-title {
				font-size: 2rem;
			}
			
			.password-form-container {
				padding: 2rem;
				margin: 0 -15px;
				border-radius: 15px;
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
				<h1 class="page-title">Change Password</h1>
				<p class="page-subtitle">Update your account password to keep your Elite Fitness account secure</p>
			</div>
		</div>
	</section>

	<!-- Change Password Section -->
	<section class="password-section">
		<div class="container">
			<div class="section-header" data-aos="fade-up">
				<h2 class="section-title">Security Update</h2>
				<p class="section-subtitle">Enter your current password and choose a new secure password</p>
			</div>
			
			<div class="password-form-container" data-aos="fade-up" data-aos-delay="200">
				<div class="security-icon">
					<i class="fas fa-shield-alt"></i>
				</div>
				
				<h3 class="form-title">Change Your Password</h3>
				
				<?php if($error){ ?>
					<div class="errorWrap">
						<i class="fas fa-exclamation-triangle me-2"></i>
						<strong>Error:</strong> <?php echo htmlentities($error); ?>
				</div>
				<?php } else if($msg){ ?>
					<div class="succWrap">
						<i class="fas fa-check-circle me-2"></i>
						<strong>Success:</strong> <?php echo htmlentities($msg); ?>
						</div>
				<?php } ?>
				
				<form method="post" class="password-form" onsubmit="return validateForm()">
					<div class="form-group">
						<label class="form-label">Current Password</label>
						<input type="password" name="password" id="password" class="form-control" placeholder="Enter your current password" autocomplete="off" required>
							</div>
							
					<div class="form-group">
						<label class="form-label">New Password</label>
						<input type="password" name="newpassword" id="newpassword" class="form-control" placeholder="Enter your new password" autocomplete="off" required>
						<div class="password-strength" id="passwordStrength"></div>
							</div>
							
					<div class="form-group">
						<label class="form-label">Confirm New Password</label>
						<input type="password" name="confirmpassword" id="confirmpassword" class="form-control" placeholder="Confirm your new password" autocomplete="off" required>
						</div>
					
					<button type="submit" id="submit" name="submit" class="btn-submit">
						<i class="fas fa-key me-2"></i>Update Password
					</button>
</form>
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
		
		// Password validation
		function validateForm() {
			const newPassword = document.getElementById('newpassword').value;
			const confirmPassword = document.getElementById('confirmpassword').value;
			
			if(newPassword !== confirmPassword) {
				alert('New Password and Confirm Password fields do not match!');
				document.getElementById('confirmpassword').focus();
				return false;
			}
			
			if(newPassword.length < 6) {
				alert('Password must be at least 6 characters long!');
				document.getElementById('newpassword').focus();
				return false;
			}
			
			return true;
		}
		
		// Password strength indicator
		document.getElementById('newpassword').addEventListener('input', function() {
			const password = this.value;
			const strengthDiv = document.getElementById('passwordStrength');
			
			if(password.length === 0) {
				strengthDiv.textContent = '';
				return;
			}
			
			let strength = 0;
			if(password.length >= 6) strength++;
			if(/[a-z]/.test(password)) strength++;
			if(/[A-Z]/.test(password)) strength++;
			if(/[0-9]/.test(password)) strength++;
			if(/[^A-Za-z0-9]/.test(password)) strength++;
			
			let strengthText = '';
			let strengthClass = '';
			
			if(strength <= 2) {
				strengthText = 'Weak password';
				strengthClass = 'strength-weak';
			} else if(strength <= 3) {
				strengthText = 'Medium strength password';
				strengthClass = 'strength-medium';
			} else {
				strengthText = 'Strong password';
				strengthClass = 'strength-strong';
			}
			
			strengthDiv.textContent = strengthText;
			strengthDiv.className = 'password-strength ' + strengthClass;
		});
		
		// Form enhancement
		document.addEventListener('DOMContentLoaded', function() {
			const inputs = document.querySelectorAll('.form-control');
			
			inputs.forEach(input => {
				input.addEventListener('focus', function() {
					this.parentElement.style.transform = 'scale(1.02)';
					this.parentElement.style.transition = 'transform 0.2s ease';
				});
				
				input.addEventListener('blur', function() {
					this.parentElement.style.transform = 'scale(1)';
				});
			});
		});
	</script>
	</body>
</html>
