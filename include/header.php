<header class="header-section">
	<!-- Header Top Bar -->
	<div class="header-top">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-md-6 d-none d-md-block">
					<div class="header-contact-info">
						<div class="contact-item">
							<i class="fas fa-map-marker-alt"></i>
							<span>Kirtipur, Kathmandu</span>
						</div>
						<div class="contact-item">
							<i class="fas fa-phone"></i>
							<span>(9840444777)</span>
						</div>
					</div>
				</div>
				<div class="col-md-6 text-end">
					<div class="header-user-actions">
						<?php if(strlen($_SESSION['uid'])==0): ?>
							<a href="login.php" class="btn-login">
								<i class="fas fa-sign-in-alt"></i>
								<span>Login</span>
							</a>
							<a href="registration.php" class="btn-register">
								<i class="fas fa-user-plus"></i>
								<span>Register</span>
							</a>
						<?php else: ?>
							<div class="user-menu">
								<a href="profile.php" class="user-menu-item">
									<i class="fas fa-user-circle"></i>
									<span>Profile</span>
								</a>
								<a href="changepassword.php" class="user-menu-item">
									<i class="fas fa-key"></i>
									<span>Password</span>
								</a>
								<a href="logout.php" class="user-menu-item logout">
									<i class="fas fa-sign-out-alt"></i>
									<span>Logout</span>
								</a>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Main Navigation -->
	<nav class="header-bottom">
		<div class="container">
			<div class="nav-wrapper">
				<!-- Logo -->
				<a href="index.php" class="site-logo">
					<div class="logo-text">
						<span class="logo-main">FIT Track HUB</span>
						<span class="logo-sub">Transform Your Body</span>
					</div>
				</a>
    
				<!-- Mobile Menu Toggle -->
				<button class="mobile-menu-toggle d-md-none" type="button">
					<span></span>
					<span></span>
					<span></span>
				</button>
    
				<!-- Navigation Menu -->
				<ul class="main-menu">
					<li><a href="index.php" class="nav-link active">Home</a></li>
					<li><a href="about.php" class="nav-link">About</a></li>
					<li><a href="contact.php" class="nav-link">Contact</a></li>
					
					<?php if(strlen($_SESSION['uid'])==0): ?>
						<li><a href="admin/" class="nav-link admin-link">Admin</a></li>
					<?php else: ?>
						<li><a href="booking-history.php" class="nav-link">My Bookings</a></li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	</nav>
</header>

<style>
/* Header Styles */
.header-section {
	position: fixed;
	top: 0;
	left: 0;
	right: 0;
	z-index: 1000;
	background: rgba(255, 255, 255, 0.95);
	backdrop-filter: blur(10px);
	box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
	transition: all 0.3s ease;
}

.header-section.scrolled {
	background: rgba(255, 255, 255, 0.98);
	box-shadow: 0 4px 30px rgba(0, 0, 0, 0.15);
}

/* Header Top Bar */
.header-top {
	background: var(--gradient-primary);
	padding: 8px 0;
	color: white;
	font-size: 0.9rem;
}

.header-contact-info {
	display: flex;
	gap: 20px;
}

.contact-item {
	display: flex;
	align-items: center;
	gap: 8px;
}

.contact-item i {
	font-size: 14px;
	color: rgba(255, 255, 255, 0.8);
}

.header-user-actions {
	display: flex;
	gap: 15px;
	justify-content: flex-end;
}

.btn-login, .btn-register {
	color: white;
	text-decoration: none;
	padding: 6px 16px;
	border-radius: 20px;
	transition: all 0.3s ease;
	display: flex;
	align-items: center;
	gap: 6px;
	font-size: 0.9rem;
	font-weight: 500;
}

.btn-login {
	background: rgba(255, 255, 255, 0.2);
	border: 1px solid rgba(255, 255, 255, 0.3);
}

.btn-register {
	background: var(--gradient-secondary);
	border: none;
}

.btn-login:hover, .btn-register:hover {
	color: white;
	transform: translateY(-1px);
	box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.user-menu {
	display: flex;
	gap: 15px;
}

.user-menu-item {
	color: white;
	text-decoration: none;
	padding: 6px 12px;
	border-radius: 15px;
	transition: all 0.3s ease;
	display: flex;
	align-items: center;
	gap: 6px;
	font-size: 0.9rem;
	background: rgba(255, 255, 255, 0.1);
}

.user-menu-item:hover {
	color: white;
	background: rgba(255, 255, 255, 0.2);
	transform: translateY(-1px);
}

.user-menu-item.logout {
	background: rgba(239, 68, 68, 0.8);
}

.user-menu-item.logout:hover {
	background: rgba(239, 68, 68, 1);
}

/* Main Navigation */
.header-bottom {
	padding: 15px 0;
	background: white;
}

.nav-wrapper {
	display: flex;
	align-items: center;
	justify-content: space-between;
}

/* Logo */
.site-logo {
	text-decoration: none;
	display: flex;
	align-items: center;
}

.logo-text {
	display: flex;
	flex-direction: column;
	line-height: 1;
}

.logo-main {
	font-family: 'Poppins', sans-serif;
	font-size: 1.8rem;
	font-weight: 800;
	background: var(--gradient-primary);
	-webkit-background-clip: text;
	-webkit-text-fill-color: transparent;
	background-clip: text;
	letter-spacing: -0.5px;
}

.logo-sub {
	font-size: 0.75rem;
	color: var(--text-secondary);
	font-weight: 500;
	margin-top: 2px;
}

/* Mobile Menu Toggle */
.mobile-menu-toggle {
	display: none;
	background: none;
	border: none;
	padding: 0;
	width: 30px;
	height: 25px;
	position: relative;
	cursor: pointer;
}

.mobile-menu-toggle span {
	display: block;
	width: 100%;
	height: 3px;
	background: var(--primary-color);
	border-radius: 3px;
	transition: all 0.3s ease;
	position: absolute;
}

.mobile-menu-toggle span:nth-child(1) { top: 0; }
.mobile-menu-toggle span:nth-child(2) { top: 50%; transform: translateY(-50%); }
.mobile-menu-toggle span:nth-child(3) { bottom: 0; }

.mobile-menu-toggle.active span:nth-child(1) {
	transform: rotate(45deg);
	top: 50%;
}

.mobile-menu-toggle.active span:nth-child(2) {
	opacity: 0;
}

.mobile-menu-toggle.active span:nth-child(3) {
	transform: rotate(-45deg);
	bottom: 50%;
}

/* Navigation Menu */
.main-menu {
	display: flex;
	list-style: none;
	margin: 0;
	padding: 0;
	gap: 30px;
	align-items: center;
}

.nav-link {
	color: var(--text-primary);
	text-decoration: none;
	font-weight: 500;
	font-size: 1rem;
	padding: 8px 0;
	position: relative;
	transition: all 0.3s ease;
}

.nav-link::after {
	content: '';
	position: absolute;
	bottom: 0;
	left: 0;
	width: 0;
	height: 2px;
	background: var(--gradient-primary);
	transition: width 0.3s ease;
}

.nav-link:hover::after,
.nav-link.active::after {
	width: 100%;
}

.nav-link:hover {
	color: var(--primary-color);
}

.nav-link.active {
	color: var(--primary-color);
}

.admin-link {
	background: var(--gradient-secondary);
	color: white;
	padding: 8px 20px;
	border-radius: 25px;
	transition: all 0.3s ease;
}

.admin-link:hover {
	color: white;
	transform: translateY(-2px);
	box-shadow: 0 5px 20px rgba(240, 147, 251, 0.4);
}

.admin-link::after {
	display: none;
}

/* Responsive Design */
@media (max-width: 768px) {
	.header-top {
		display: none;
	}
	
	.header-bottom {
		padding: 10px 0;
	}
	
	.logo-main {
		font-size: 1.5rem;
	}
	
	.logo-sub {
		font-size: 0.7rem;
	}
	
	.mobile-menu-toggle {
		display: block;
	}
	
	.main-menu {
		position: fixed;
		top: 100%;
		left: 0;
		right: 0;
		background: white;
		flex-direction: column;
		padding: 20px;
		gap: 15px;
		box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
		transform: translateY(-100%);
		opacity: 0;
		visibility: hidden;
		transition: all 0.3s ease;
	}
	
	.main-menu.active {
		transform: translateY(0);
		opacity: 1;
		visibility: visible;
	}
	
	.nav-link {
		padding: 12px 0;
		width: 100%;
		text-align: center;
		border-bottom: 1px solid #f0f0f0;
	}
	
	.nav-link:last-child {
		border-bottom: none;
	}
}

@media (max-width: 480px) {
	.logo-main {
		font-size: 1.3rem;
	}
	
	.logo-sub {
		font-size: 0.65rem;
	}
}
</style>

<script>
// Header scroll effect
window.addEventListener('scroll', function() {
	const header = document.querySelector('.header-section');
	if (window.scrollY > 100) {
		header.classList.add('scrolled');
	} else {
		header.classList.remove('scrolled');
	}
});

// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
	const mobileToggle = document.querySelector('.mobile-menu-toggle');
	const mainMenu = document.querySelector('.main-menu');
	
	if (mobileToggle && mainMenu) {
		mobileToggle.addEventListener('click', function() {
			this.classList.toggle('active');
			mainMenu.classList.toggle('active');
		});
		
		// Close menu when clicking on a link
		const navLinks = document.querySelectorAll('.nav-link');
		navLinks.forEach(link => {
			link.addEventListener('click', function() {
				mobileToggle.classList.remove('active');
				mainMenu.classList.remove('active');
			});
		});
	}
});
</script>