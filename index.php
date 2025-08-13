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
	<title>FIT TRACK HUB | Premium Gym & Fitness Center</title>
	<meta charset="UTF-8">
	<meta name="description" content="FIT TRACK HUB - Premium gym with state-of-the-art facilities, expert trainers, and personalized fitness programs. Transform your life today!">
	<meta name="keywords" content="gym, fitness, workout, training, health, wellness, personal training, weight loss, muscle gain">
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
		}
		
		h1, h2, h3, h4, h5, h6 {
			font-family: 'Poppins', sans-serif;
			font-weight: 700;
			line-height: 1.2;
		}
		
		.section-padding {
			padding: 80px 0;
		}
		
		/* Hero Section */
		.hero-section {
			background: var(--gradient-primary);
			color: white;
			padding: 120px 0 80px;
			position: relative;
			overflow: hidden;
		}
		
		.hero-section::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background: url('img/hero-slider/1.png') center/cover;
			opacity: 0.1;
			z-index: 1;
		}
		
		.hero-content {
			position: relative;
			z-index: 2;
		}
		
		.hero-title {
			font-size: 4rem;
			font-weight: 800;
			margin-bottom: 1.5rem;
			text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
		}
		
		.hero-subtitle {
			font-size: 1.3rem;
			margin-bottom: 2rem;
			opacity: 0.9;
			max-width: 600px;
		}
		
		.btn-hero {
			background: var(--gradient-secondary);
			border: none;
			color: white;
			padding: 15px 40px;
			border-radius: 30px;
			font-weight: 600;
			font-size: 1.1rem;
			text-decoration: none;
			display: inline-block;
			transition: all 0.3s ease;
			margin-right: 15px;
			margin-bottom: 15px;
		}
		
		.btn-hero:hover {
			color: white;
			transform: translateY(-3px);
			box-shadow: 0 15px 40px rgba(240, 147, 251, 0.4);
		}
		
		.btn-outline {
			background: transparent;
			border: 2px solid white;
			color: white;
		}
		
		.btn-outline:hover {
			background: white;
			color: var(--primary-color);
		}
		
		/* Features Section */
		.features-section {
			padding: 80px 0;
			background: white;
		}
		
		.section-title {
			text-align: center;
			margin-bottom: 3rem;
		}
		
		.section-title h2 {
			font-size: 3rem;
			margin-bottom: 1rem;
			background: var(--gradient-primary);
			-webkit-background-clip: text;
			-webkit-text-fill-color: transparent;
			background-clip: text;
		}
		
		.section-title p {
			color: var(--text-secondary);
			font-size: 1.2rem;
			max-width: 600px;
			margin: 0 auto;
		}
		
		.feature-card {
			background: white;
			padding: 2.5rem;
			border-radius: 20px;
			box-shadow: 0 10px 40px rgba(0,0,0,0.1);
			text-align: center;
			transition: all 0.3s ease;
			height: 100%;
			border: 1px solid rgba(0,0,0,0.05);
		}
		
		.feature-card:hover {
			transform: translateY(-10px);
			box-shadow: 0 20px 60px rgba(0,0,0,0.15);
		}
		
		.feature-icon {
			width: 80px;
			height: 80px;
			background: var(--gradient-primary);
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			margin: 0 auto 1.5rem;
			font-size: 2rem;
			color: white;
		}
		
		.feature-card h4 {
			font-size: 1.5rem;
			margin-bottom: 1rem;
			color: var(--text-primary);
		}
		
		.feature-card p {
			color: var(--text-secondary);
			line-height: 1.6;
		}
		
		/* Stats Section */
		.stats-section {
			background: var(--gradient-secondary);
			padding: 80px 0;
			color: white;
		}
		
		.stat-item {
			text-align: center;
		}
		
		.stat-number {
			font-size: 3.5rem;
			font-weight: 800;
			margin-bottom: 0.5rem;
			display: block;
		}
		
		.stat-label {
			font-size: 1.1rem;
			opacity: 0.9;
		}
		
		/* Pricing Section */
		.pricing-section {
			padding: 80px 0;
			background: white;
		}
		
		.pricing-card {
			background: white;
			border-radius: 25px;
			padding: 2.5rem;
			box-shadow: 0 15px 50px rgba(0,0,0,0.1);
			text-align: center;
			transition: all 0.3s ease;
			height: 100%;
			border: 1px solid rgba(0,0,0,0.05);
			position: relative;
			overflow: hidden;
		}
		
		.pricing-card.featured {
			border: 3px solid var(--primary-color);
			transform: scale(1.05);
		}
		
		.pricing-card:hover {
			transform: translateY(-10px);
			box-shadow: 0 25px 70px rgba(0,0,0,0.15);
		}
		
		.pricing-card.featured:hover {
			transform: scale(1.05) translateY(-10px);
		}
		
		.package-name {
			font-size: 1.5rem;
			font-weight: 700;
			margin-bottom: 1rem;
			color: var(--text-primary);
		}
		
		.package-price {
			font-size: 3rem;
			font-weight: 800;
			color: var(--primary-color);
			margin-bottom: 0.5rem;
		}
		
		.package-duration {
			color: var(--text-secondary);
			margin-bottom: 1.5rem;
			font-size: 1.1rem;
		}
		
		.package-description {
			color: var(--text-secondary);
			margin-bottom: 2rem;
			line-height: 1.6;
		}
		
		.btn-book {
			background: var(--gradient-primary);
			border: none;
			color: white;
			padding: 15px 40px;
			border-radius: 25px;
			font-weight: 600;
			font-size: 1.1rem;
			transition: all 0.3s ease;
			width: 100%;
			cursor: pointer;
		}
		
		.btn-book:hover {
			transform: translateY(-2px);
			box-shadow: 0 10px 30px rgba(99, 102, 241, 0.4);
		}
		
		.btn-login {
			background: var(--gradient-secondary);
		}
		
		.btn-login:hover {
			box-shadow: 0 10px 30px rgba(240, 147, 251, 0.4);
		}
		
		.btn-already-booked {
			background: var(--text-secondary);
			cursor: not-allowed;
		}
		
		.btn-already-booked:hover {
			transform: none;
			box-shadow: none;
		}
		
		/* Responsive Design */
		@media (max-width: 768px) {
			.hero-title {
				font-size: 2.5rem;
			}
			
			.section-title h2 {
				font-size: 2.5rem;
			}
			
			.stat-number {
				font-size: 2.5rem;
			}
			
			.pricing-card.featured {
				transform: none;
				margin-bottom: 2rem;
			}
		}
		
		/* Success/Error Messages */
		.message-container {
			position: fixed;
			top: 100px;
			right: 20px;
			z-index: 1000;
			max-width: 400px;
		}
		
		.message {
			padding: 15px 20px;
			margin-bottom: 10px;
			border-radius: 10px;
			color: white;
			font-weight: 500;
			box-shadow: 0 5px 20px rgba(0,0,0,0.2);
			animation: slideIn 0.3s ease;
		}
		
		.message.success {
			background: #10b981;
		}
		
		.message.error {
			background: var(--accent-color);
		}
		
		@keyframes slideIn {
			from {
				transform: translateX(100%);
				opacity: 0;
			}
			to {
				transform: translateX(0);
				opacity: 1;
			}
		}
	</style>
</head>
<body>
	<!-- Header Section -->
	<?php include 'include/header.php';?>

	<!-- Success/Error Messages -->
	<?php if($msg || $error): ?>
	<div class="message-container">
		<?php if($msg): ?>
			<div class="message success">
				<i class="fas fa-check-circle me-2"></i><?php echo htmlentities($msg); ?>
			</div>
		<?php endif; ?>
		<?php if($error): ?>
			<div class="message error">
				<i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlentities($error); ?>
			</div>
		<?php endif; ?>
	</div>
	<?php endif; ?>

	<!-- Hero Section -->
	<section class="hero-section">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-lg-6">
					<div class="hero-content" data-aos="fade-right">
						<h1 class="hero-title">Transform Your Life with FIT TRACK HUB</h1>
						<p class="hero-subtitle">Join the ultimate fitness experience with state-of-the-art facilities, expert trainers, and personalized programs designed to help you achieve your goals.</p>
						<div class="hero-buttons">
							<a href="#pricing" class="btn-hero">
								<i class="fas fa-dumbbell me-2"></i>Get Started
							</a>
							<a href="about.php" class="btn-hero btn-outline">
								<i class="fas fa-info-circle me-2"></i>Learn More
							</a>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="hero-image" data-aos="fade-left">
						<img src="img/hero-slider/1.png" alt="FIT TRACK HUB Hero" class="img-fluid">
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Features Section -->
	<section class="features-section section-padding">
		<div class="container">
			<div class="section-title" data-aos="fade-up">
				<h2>Why Choose FIT TRACK HUB?</h2>
				<p>Discover what makes us the premier choice for fitness enthusiasts and beginners alike</p>
			</div>
			
			<div class="row">
				<div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
					<div class="feature-card">
						<div class="feature-icon">
							<i class="fas fa-dumbbell"></i>
						</div>
						<h4>State-of-the-Art Equipment</h4>
						<p>Access to the latest fitness equipment and technology to maximize your workout efficiency and results.</p>
					</div>
				</div>
				<div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
					<div class="feature-card">
						<div class="feature-icon">
							<i class="fas fa-user-friends"></i>
						</div>
						<h4>Expert Trainers</h4>
						<p>Certified personal trainers with years of experience to guide you through your fitness journey safely and effectively.</p>
					</div>
				</div>
				<div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
					<div class="feature-card">
						<div class="feature-icon">
							<i class="fas fa-clock"></i>
						</div>
						<h4>Flexible Hours</h4>
						<p>Open 24/7 to accommodate your busy schedule, ensuring you never miss a workout session.</p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Stats Section -->
	<section class="stats-section section-padding">
		<div class="container">
			<div class="row">
				<div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
					<div class="stat-item">
						<span class="stat-number">500+</span>
						<span class="stat-label">Active Members</span>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
					<div class="stat-item">
						<span class="stat-number">50+</span>
						<span class="stat-label">Expert Trainers</span>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
					<div class="stat-item">
						<span class="stat-number">100+</span>
						<span class="stat-label">Fitness Classes</span>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
					<div class="stat-item">
						<span class="stat-number">5+</span>
						<span class="stat-label">Years Experience</span>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Pricing Section -->
	<section id="pricing" class="pricing-section section-padding">
		<div class="container">
			<div class="section-title" data-aos="fade-up">
				<h2>Choose Your Plan</h2>
				<p>Select the perfect fitness package that fits your lifestyle and goals. Start your transformation today!</p>
			</div>
			
			<div class="row">
				        <?php 
				$sql = "SELECT id, category, titlename, PackageType, PackageDuratiobn, Price, uploadphoto, Description, create_date FROM tbladdpackage";
				$query = $dbh->prepare($sql);
				$query->execute();
				$results = $query->fetchAll(PDO::FETCH_OBJ);
				$cnt = 1;
				if($query->rowCount() > 0) {
					foreach($results as $result) {
						$isFeatured = ($cnt == 2) ? 'featured' : '';
						
						// Check if user has already booked this package
						$alreadyBooked = false;
						if(strlen($_SESSION['uid']) > 0) {
							$check_booking = "SELECT id FROM tblbooking WHERE package_id=:pid AND userid=:uid";
							$check_query = $dbh->prepare($check_booking);
							$check_query->bindParam(':pid', $result->id, PDO::PARAM_STR);
							$check_query->bindParam(':uid', $_SESSION['uid'], PDO::PARAM_STR);
							$check_query->execute();
							$alreadyBooked = ($check_query->rowCount() > 0);
						}
				?>
				<div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?php echo $cnt * 100; ?>">
					<div class="pricing-card <?php echo $isFeatured; ?>">
						<h4 class="package-name"><?php echo htmlentities($result->titlename); ?></h4>
						<div class="package-price">$<?php echo htmlentities($result->Price); ?></div>
						<div class="package-duration"><?php echo htmlentities($result->PackageDuratiobn); ?></div>
						<div class="package-description">
							<?php echo htmlentities($result->Description); ?>
						</div>
						
						<?php if(strlen($_SESSION['uid']) == 0): ?>
						<a href="login.php" class="btn-book btn-login">
							<i class="fas fa-sign-in-alt me-2"></i>Login to Book
						</a>
						<?php elseif($alreadyBooked): ?>
						<button class="btn-book btn-already-booked" disabled>
							<i class="fas fa-check-circle me-2"></i>Already Booked
						</button>
						<?php else: ?>
							 <form method='post'>
							<input type='hidden' name='pid' value='<?php echo htmlentities($result->id); ?>'>
							<button class='btn-book' type='submit' name='submit' onclick="return confirm('Are you sure you want to book this package?');">
								<i class="fas fa-calendar-check me-2"></i>Book Now
							</button>
                        </form> 
						<?php endif; ?>
					</div>
				</div>
				<?php 
					$cnt = $cnt + 1; 
					} 
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
		
		// Smooth scrolling for anchor links
		document.querySelectorAll('a[href^="#"]').forEach(anchor => {
			anchor.addEventListener('click', function (e) {
				e.preventDefault();
				const target = document.querySelector(this.getAttribute('href'));
				if (target) {
					target.scrollIntoView({
						behavior: 'smooth',
						block: 'start'
					});
				}
			});
		});
		
		// Auto-hide messages after 5 seconds
		setTimeout(function() {
			const messages = document.querySelectorAll('.message');
			messages.forEach(message => {
				message.style.opacity = '0';
				setTimeout(() => message.remove(), 300);
			});
		}, 5000);
	</script>
	</body>
</html>
