
<?php
session_start();
error_reporting(0);
require_once('include/config.php');

$msg = ""; 
$error = "";

if(isset($_POST['submit'])) {
  $email = trim($_POST['email']);
  $password = md5(($_POST['password']));
    
  if($email != "" && $password != "") {
    try {
            $query = "SELECT id, fname, lname, email, mobile, password, address, create_date FROM tbluser WHERE email=:email AND password=:password";
      $stmt = $dbh->prepare($query);
      $stmt->bindParam('email', $email, PDO::PARAM_STR);
      $stmt->bindValue('password', $password, PDO::PARAM_STR);
      $stmt->execute();
      $count = $stmt->rowCount();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
      if($count == 1 && !empty($row)) {
                $_SESSION['uid'] = $row['id'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['name'] = $row['fname'];
       header("location: index.php");
                exit();
      } else {
                $msg = "Invalid email or password!";
      }
    } catch (PDOException $e) {
            $error = "Database error. Please try again.";
    }
  } else {
    $msg = "Both fields are required!";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Login | Elite Fitness</title>
	<meta charset="UTF-8">
	<meta name="description" content="Login to your Elite Fitness account - Access your fitness journey and manage your memberships">
	<meta name="keywords" content="login, fitness, gym, membership, account">
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
		
		/* Login Section */
		.login-section {
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
		
		/* Login Form Container */
		.login-form-container {
			background: white;
			border-radius: 25px;
			padding: 3rem;
			box-shadow: 0 15px 50px rgba(0,0,0,0.1);
			border: 1px solid rgba(0,0,0,0.05);
			position: relative;
			overflow: hidden;
			max-width: 500px;
			margin: 0 auto;
		}
		
		.login-form-container::before {
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

		.login-btn{
			background: var(--gradient-secondary);
    color: white;
    border: none;
    padding: 6px 40px;
    border-radius: 25px;
    font-weight: 400;
    font-size: 1.1rem;
	 cursor: pointer;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;            
		}

.btn-login {
    background: #000000;       /* Solid black background */
    color: #4facfe;            /* White text, always visible */
    border: none;
    padding: 15px 40px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 1.1rem;
    cursor: pointer;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;                 /* Space between icon and text */
    /* transition: all 0.3s ease; */
}
 
.btn-login i {
    transition: transform 0.3s ease;
}

.btn-login:hover {
    background: #1a1a1a;       /* Slightly lighter black on hover */
    transform: translateY(-2px); /* Small lift effect */
    box-shadow: 0 8px 25px rgba(0,0,0,0.4);
}

.btn-login:hover i {
    transform: translateX(5px); /* Icon slides slightly right on hover */
}



		.btn-register {
			background: var(--gradient-secondary);
			border: none;
			color: white;
			padding: 15px 40px;
			border-radius: 25px;
			font-weight: 600;
			font-size: 1.1rem;
			text-decoration: none;
			display: block;
			text-align: center;
			transition: all 0.3s ease;
			width: 100%;
		}
		
		.btn-register:hover {
			color: black;
			transform: translateY(-2px);
			box-shadow: 0 10px 30px rgba(240, 147, 251, 0.4);
		}
		
		/* Login Icon */
		.login-icon {
			color: black;
			text-align: center;
			margin-bottom: 2rem;
		}
		
		.login-icon i {
			font-size: 4rem;
			color: var(--primary-color);
			opacity: 0.8;
		}
		
		/* Divider */
		.form-divider {
			text-align: center;
			margin: 1.5rem 0;
			position: relative;
		}

		
		.form-divider span {
			background: white;
			padding: 0 1rem;
			color: var(--text-secondary);
			font-size: 0.9rem;
		}
		
		/* Responsive Design */
		@media (max-width: 768px) {
			.page-title {
				font-size: 2.5rem;
			}
			
			.section-title {
				font-size: 2rem;
			}
			
			.login-form-container {
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
		
		/* Error Message Below Fields */
		.error-message {
			color: var(--accent-color);
			font-size: 0.85rem;
			margin-top: 5px;
			display: none;
			min-height: 20px;
		}
		
		.error-message.show {
			display: block;
		}
		
		.form-control.error-field {
			border-color: var(--accent-color);
			box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
		}
	</style>
</head>
<body>
	<!-- Header Section -->
	<?php include 'include/header.php';?>

	<!-- Page Header -->


	<!-- Login Section -->
	<section class="login-section">
		<div class="container">
			<div class="section-header" data-aos="fade-up">
				<h2 class="section-title">Account Access</h2>
				<p class="section-subtitle">Enter your credentials to access your fitness dashboard</p>
			</div>
			
			<div class="login-form-container" data-aos="fade-up" data-aos-delay="200">
				<div class="login-icon">
					<i class="fas fa-user-circle"></i>
				</div>
				
				<h3 class="form-title">User Login</h3>
				
				<?php if($error){ ?>
					<div class="errorWrap">
						<i class="fas fa-exclamation-triangle me-2"></i>
						<strong>Error:</strong> <?php echo htmlentities($error); ?>
					</div>
				<?php } else if($msg){ ?>
					<div class="errorWrap">
						<i class="fas fa-exclamation-circle me-2"></i>
						<strong>Error:</strong> <?php echo htmlentities($msg); ?>
					</div>
				<?php } ?>
				
				<form method="post" class="login-form" id="loginForm">
					<div class="form-group">
						<label class="form-label">Email Address</label>
						<input type="email" name="email" id="email" class="form-control" placeholder="Enter your email address" autocomplete="off" value="<?php echo isset($_POST['email']) ? htmlentities($_POST['email']) : ''; ?>" required>
						<div class="error-message" id="email-error"></div>
					</div>
					
					<div class="form-group">
						<label class="form-label">Password</label>
						<input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" autocomplete="off" required>
						<div class="error-message" id="password-error"></div>
					</div>
					<button type="submit" id="submit" name="submit" class="login-btn">
						<i class="fas fa-sign-in-alt me-2"></i>Login
					</button>
					<div class="form-divider">
						<span>New to Elite Fitness?</span>
				</div>
	
					<a href="registration.php" class="btn-register">
						<i class="fas fa-user-plus me-2"></i>Create Account
					</a>
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
		
		// Error message functions
		function showError(fieldId, message) {
			var field = document.getElementById(fieldId);
			var errorDiv = document.getElementById(fieldId + "-error");
			if(errorDiv) {
				errorDiv.textContent = message;
				errorDiv.classList.add("show");
				field.classList.add("error-field");
			}
		}
		
		function clearError(fieldId) {
			var field = document.getElementById(fieldId);
			var errorDiv = document.getElementById(fieldId + "-error");
			if(errorDiv) {
				errorDiv.textContent = "";
				errorDiv.classList.remove("show");
				field.classList.remove("error-field");
			}
		}
		
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
				
				// Clear errors on input
				input.addEventListener('input', function() {
					clearError(this.id);
				});
			});
			
			// Form validation
			const form = document.getElementById('loginForm');
			if (form) {
				form.addEventListener('submit', function(e) {
					const email = document.getElementById('email').value.trim();
					const password = document.getElementById('password').value.trim();
					const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9-]+\.[a-zA-Z]{2,}$/;
					
					// Clear all errors first
					clearError('email');
					clearError('password');
					
					var valid = true;
					
					if (!email) {
						showError('email', 'Email address is required');
						valid = false;
					} else if (!emailPattern.test(email)) {
						showError('email', 'Please enter a valid email address (for example: name@example.com)!');
						valid = false;
					}
					
					if (!password) {
						showError('password', 'Password is required');
						valid = false;
					}
					
					if (!valid) {
						e.preventDefault();
						// Scroll to first error
						var firstError = document.querySelector(".error-message.show");
						if(firstError) {
							firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
						}
						return false;
					}
				});
			}
			
			// Show server-side errors below fields
			<?php if($msg && !$error) { ?>
				// If it's a login error (invalid credentials), show it below password field
				showError('password', '<?php echo htmlentities($msg); ?>');
			<?php } ?>
		});
	</script>
	</body>
</html>
