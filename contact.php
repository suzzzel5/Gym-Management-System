<?php 
session_start();
error_reporting(0);
include 'include/config.php';
$uid = $_SESSION['uid'];
$msg = "";
$error = "";

if(isset($_POST['submit'])) { 
    $pid = $_POST['pid'];
    
    // Check if user has already booked this package
    $check_sql = "SELECT id FROM tblbooking WHERE package_id=:pid AND userid=:uid";
    $check_query = $dbh->prepare($check_sql);
    $check_query->bindParam(':pid', $pid, PDO::PARAM_STR);
    $check_query->bindParam(':uid', $uid, PDO::PARAM_STR);
    $check_query->execute();
    
    if($check_query->rowCount() > 0) {
        $error = "You have already booked this package!";
    } else {
        // Proceed with booking if not already booked
        $sql = "INSERT INTO tblbooking (package_id, userid) VALUES (:pid, :uid)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':pid', $pid, PDO::PARAM_STR);
        $query->bindParam(':uid', $uid, PDO::PARAM_STR);
        
        if($query->execute()) {
            $msg = "Package has been booked successfully!";
            echo "<script>alert('Package has been booked successfully!');</script>";
            echo "<script>window.location.href='Booking-History.php';</script>";
        } else {
            $error = "Error booking package. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Contact Us | Elite Fitness</title>
	<meta charset="UTF-8">
	<meta name="description" content="Get in touch with Elite Fitness - Contact us for membership inquiries, training programs, and fitness consultation">
	<meta name="keywords" content="contact, fitness, gym, membership, training, consultation">
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
		
		/* Contact Section */
		.contact-section {
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
		
		/* Contact Cards */
		.contact-cards {
			margin-bottom: 4rem;
		}
		
		.contact-card {
			background: white;
			padding: 2rem;
			border-radius: 20px;
			box-shadow: 0 10px 40px rgba(0,0,0,0.1);
			text-align: center;
			transition: all 0.3s ease;
			height: 100%;
			border: 1px solid rgba(0,0,0,0.05);
		}
		
		.contact-card:hover {
			transform: translateY(-5px);
			box-shadow: 0 20px 60px rgba(0,0,0,0.15);
		}
		
		.contact-icon {
			width: 70px;
			height: 70px;
			background: var(--gradient-primary);
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			margin: 0 auto 1.5rem;
			font-size: 1.8rem;
			color: white;
		}
		
		.contact-card h4 {
			font-size: 1.3rem;
			margin-bottom: 1rem;
			color: var(--text-primary);
		}
		
		.contact-card p {
			color: var(--text-secondary);
			margin-bottom: 0;
		}
		
		/* Contact Form */
		.contact-form-container {
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
		
		.contact-form-container::before {
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
		
		textarea.form-control {
			resize: vertical;
			min-height: 120px;
		}
		
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
		}
		
		.btn-submit:hover {
			transform: translateY(-2px);
			box-shadow: 0 10px 30px rgba(240, 147, 251, 0.4);
		}
		
		/* Map Section */
		.map-section {
			padding: 80px 0;
			background: var(--light-color);
		}
		
		.map-placeholder {
			background: white;
			border-radius: 20px;
			padding: 3rem;
			text-align: center;
			box-shadow: 0 10px 40px rgba(0,0,0,0.1);
			border: 1px solid rgba(0,0,0,0.05);
		}
		
		.map-icon {
			font-size: 4rem;
			color: var(--primary-color);
			margin-bottom: 1rem;
			opacity: 0.8;
		}
		
		.map-placeholder h3 {
			font-size: 1.5rem;
			margin-bottom: 1rem;
			color: var(--text-primary);
		}
		
		.map-placeholder p {
			color: var(--text-secondary);
			margin-bottom: 0;
		}
		
		/* Responsive Design */
		@media (max-width: 768px) {
			.page-title {
				font-size: 2.5rem;
			}
			
			.section-title {
				font-size: 2rem;
			}
			
			.contact-form-container {
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
				<h1 class="page-title">Contact Us</h1>
				<p class="page-subtitle">Get in touch with our team for any questions about memberships, training programs, or fitness consultation</p>
			</div>
		</div>
	</section>

	<!-- Contact Section -->
	<section class="contact-section">
		<div class="container">
			<div class="section-header" data-aos="fade-up">
				<h2 class="section-title">Get In Touch</h2>
				<p class="section-subtitle">We're here to help you on your fitness journey. Reach out to us anytime!</p>
			</div>
			
			<!-- Contact Cards -->
			<div class="row contact-cards">
				<div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
					<div class="contact-card">
						<div class="contact-icon">
							<i class="fas fa-envelope"></i>
						</div>
						<h4>Email Us</h4>
						<p>info@elitefitness.com</p>
					</div>
				</div>
				<div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
					<div class="contact-card">
						<div class="contact-icon">
							<i class="fas fa-phone"></i>
						</div>
						<h4>Call Us</h4>
						<p>+1 (555) 123-4567</p>
					</div>
				</div>
				<div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
					<div class="contact-card">
						<div class="contact-icon">
							<i class="fas fa-map-marker-alt"></i>
						</div>
						<h4>Visit Us</h4>
						<p>123 Fitness Street<br>Health City, HC 12345</p>
					</div>
				</div>
			</div>
			
			<!-- Contact Form -->
			<div class="contact-form-container" data-aos="fade-up" data-aos-delay="400">
				<h3 class="form-title">Send Us a Message</h3>
				
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
				
				<form class="contact-form" method="post">
			<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-label">Your Name</label>
								<input type="text" name="name" class="form-control" placeholder="Enter your full name" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-label">Your Email</label>
								<input type="email" name="email" class="form-control" placeholder="Enter your email address" required>
				</div>
						</div>
					</div>
					
					<div class="form-group">
						<label class="form-label">Subject</label>
						<input type="text" name="subject" class="form-control" placeholder="Enter message subject" required>
					</div>
					
					<div class="form-group">
						<label class="form-label">Message</label>
						<textarea name="message" class="form-control" placeholder="Enter your message here..." required></textarea>
					</div>
					
					<button type="submit" name="contact_submit" class="btn-submit">
						<i class="fas fa-paper-plane me-2"></i>Send Message
					</button>
				</form>
			</div>
		</div>
	</section>
	
	<!-- Map Section -->
	<section class="map-section">
		<div class="container">
			<div class="map-placeholder" data-aos="fade-up">
				<div class="map-icon">
					<i class="fas fa-map"></i>
				</div>
				<h3>Our Location</h3>
				<p>Interactive map will be integrated here to show our gym location and directions.</p>
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
			
			// Basic form validation
			const form = document.querySelector('.contact-form');
			if (form) {
				form.addEventListener('submit', function(e) {
					const name = document.querySelector('input[name="name"]').value.trim();
					const email = document.querySelector('input[name="email"]').value.trim();
					const subject = document.querySelector('input[name="subject"]').value.trim();
					const message = document.querySelector('textarea[name="message"]').value.trim();
					
					if (!name || !email || !subject || !message) {
						e.preventDefault();
						alert('Please fill in all required fields.');
						return false;
					}
					
					if (!email.includes('@')) {
						e.preventDefault();
						alert('Please enter a valid email address.');
						document.querySelector('input[name="email"]').focus();
						return false;
					}
				});
			}
		});
	</script>
	</body>
</html>
