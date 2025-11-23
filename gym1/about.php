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
	<title>About Us |FIT TRACK HUB</title>
	<meta charset="UTF-8">
	<meta name="description" content="Learn about Elite Fitness - Your trusted partner in achieving fitness goals with state-of-the-art facilities and expert guidance">
	<meta name="keywords" content="about, fitness, gym, health, wellness, training">
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
		
		/* About Section */
		.about-section {
			padding: 80px 0;
			background: white;
		}
		
		.section-header {
			text-align: center;
			margin-bottom: 4rem;
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
		
		/* About Content */
		.about-content {
			background: white;
			border-radius: 25px;
			padding: 3rem;
			box-shadow: 0 15px 50px rgba(0,0,0,0.1);
			border: 1px solid rgba(0,0,0,0.05);
			position: relative;
			overflow: hidden;
		}
		
		.about-content::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			height: 5px;
			background: var(--gradient-primary);
		}
		
		.about-text {
			font-size: 1.1rem;
			line-height: 1.8;
			color: var(--text-primary);
			margin-bottom: 2rem;
		}
		
		.about-text strong {
			color: var(--primary-color);
		}
		
		/* Features Grid */
		.features-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
			gap: 2rem;
			margin-top: 3rem;
		}
		
		.feature-item {
			text-align: center;
			padding: 2rem 1.5rem;
			background: var(--light-color);
			border-radius: 20px;
			transition: all 0.3s ease;
		}
		
		.feature-item:hover {
			transform: translateY(-5px);
			box-shadow: 0 10px 30px rgba(0,0,0,0.1);
		}
		
		.feature-icon {
			width: 70px;
			height: 70px;
			background: var(--gradient-accent);
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			margin: 0 auto 1.5rem;
			font-size: 1.8rem;
			color: white;
		}
		
		.feature-title {
			font-size: 1.2rem;
			font-weight: 600;
			margin-bottom: 1rem;
			color: var(--text-primary);
		}
		
		.feature-description {
			color: var(--text-secondary);
			font-size: 0.95rem;
			line-height: 1.6;
		}
		
		/* Stats Section */
		.stats-section {
			background: var(--gradient-secondary);
			color: white;
			padding: 60px 0;
			margin-top: 4rem;
		}
		
		.stats-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
			gap: 2rem;
		}
		
		.stat-item {
			text-align: center;
		}
		
		.stat-number {
			font-size: 3rem;
			font-weight: 800;
			margin-bottom: 0.5rem;
		}
		
		.stat-label {
			font-size: 1.1rem;
			opacity: 0.9;
		}
		
		/* Responsive */
		@media (max-width: 768px) {
			.page-title {
				font-size: 2.5rem;
			}
			
			.section-title {
				font-size: 2rem;
			}
			
			.about-content {
				padding: 2rem;
				margin: 0 -15px;
				border-radius: 15px;
			}
			
			.features-grid {
				grid-template-columns: 1fr;
				gap: 1.5rem;
			}
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
				<h1 class="page-title">About FIT TRACK HUB</h1>
				<p class="page-subtitle">Discover our story, mission, and commitment to transforming lives through fitness and wellness</p>
			</div>
		</div>
	</section>

	<!-- About Section -->
	<section class="about-section">
		<div class="container">
			<div class="section-header" data-aos="fade-up">
				<h2 class="section-title">Our Story</h2>
				<p class="section-subtitle">Building a community of fitness enthusiasts and helping people achieve their health goals</p>
			</div>
			
			<div class="row">
				<div class="col-lg-12">
					<div class="about-content" data-aos="fade-up" data-aos-delay="200">
						<div class="about-text">
							<p>Welcome to <strong>FIT TRACK HUB</strong>, where passion meets purpose in the pursuit of health and wellness. We are a dedicated team of fitness professionals, entrepreneurs, and wellness enthusiasts who believe that everyone deserves access to world-class fitness facilities and expert guidance.</p>
							
							<p>Our journey began with a simple yet powerful vision: to create a fitness environment that goes beyond traditional gym experiences. We wanted to build a community where individuals of all fitness levels could feel supported, motivated, and empowered to achieve their health and fitness goals.</p>
							
							<p>At <strong>FIT TRACK HUB</strong>, we understand that fitness is not just about physical strength—it's about mental resilience, emotional well-being, and creating sustainable lifestyle changes. Our comprehensive approach combines cutting-edge equipment, personalized training programs, and a supportive community atmosphere.</p>
						</div>
						
						<div class="features-grid">
							<div class="feature-item" data-aos="fade-up" data-aos-delay="300">
								<div class="feature-icon">
									<i class="fas fa-dumbbell"></i>
								</div>
								<h4 class="feature-title">State-of-the-Art Equipment</h4>
								<p class="feature-description">Access to the latest fitness technology and premium equipment for optimal results</p>
							</div>
							
							<div class="feature-item" data-aos="fade-up" data-aos-delay="400">
								<div class="feature-icon">
									<i class="fas fa-user-graduate"></i>
								</div>
								<h4 class="feature-title">Expert Trainers</h4>
								<p class="feature-description">Certified professionals dedicated to guiding your fitness journey with personalized attention</p>
							</div>
							
							<div class="feature-item" data-aos="fade-up" data-aos-delay="500">
								<div class="feature-icon">
									<i class="fas fa-users"></i>
								</div>
								<h4 class="feature-title">Supportive Community</h4>
								<p class="feature-description">Join a network of like-minded individuals who share your passion for health and fitness</p>
							</div>
							
							<div class="feature-item" data-aos="fade-up" data-aos-delay="600">
								<div class="feature-icon">
									<i class="fas fa-heart"></i>
								</div>
								<h4 class="feature-title">Holistic Wellness</h4>
								<p class="feature-description">Comprehensive approach to health including nutrition, recovery, and mental well-being</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Stats Section -->
	<section class="stats-section">
		<div class="container">
			<div class="stats-grid">
				<div class="stat-item" data-aos="fade-up">
					<div class="stat-number">500+</div>
					<div class="stat-label">Active Members</div>
				</div>
				<div class="stat-item" data-aos="fade-up" data-aos-delay="100">
					<div class="stat-number">50+</div>
					<div class="stat-label">Expert Trainers</div>
				</div>
				<div class="stat-item" data-aos="fade-up" data-aos-delay="200">
					<div class="stat-number">100+</div>
					<div class="stat-label">Weekly Classes</div>
			</div>
				<div class="stat-item" data-aos="fade-up" data-aos-delay="300">
					<div class="stat-number">5+</div>
					<div class="stat-label">Years Experience</div>
				</div>
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
	</script>
	</body>
</html>
