<?php
error_reporting(0);
require_once('include/config.php');

$fname = "";
$lname = "";
$mobile = "";
$email = "";
$state = "";
$city = "";
$error = "";
$succmsg = "";

if(isset($_POST['submit'])) { 
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $mobile = trim($_POST['mobile']);
    $email = trim($_POST['email']);
    $state = trim($_POST['state']);
    $city = trim($_POST['city']);
    $Password = $_POST['password'];
    $pass = md5($Password);
$RepeatPassword = $_POST['RepeatPassword'];

// Email id Already Exit
    $usermatch = $dbh->prepare("SELECT mobile, email FROM tbluser WHERE (email=:usreml || mobile=:mblenmbr)");
    $usermatch->execute(array(':usreml'=>$email, ':mblenmbr'=>$mobile)); 
    
    $usrdbeml = "";
    $usrdbmble = "";
    
    while($row = $usermatch->fetch(PDO::FETCH_ASSOC)) {
        $usrdbeml = $row['email'];
        $usrdbmble = $row['mobile'];
    }

    if(empty($fname)) {
        $error = "Please Enter First Name";
    } else if(empty($mobile)) {
        $error = "Please Enter Mobile No";
    } else if(empty($email)) {
        $error = "Please Enter Email";
    } else if(!preg_match("/\.com$/i", $email)) {
        $error = "Email address must end with .com";
    } else if($email == $usrdbeml || $mobile == $usrdbmble) {
        $error = "Email Id or Mobile Number Already Exists!";
    } else if($Password == "" || $RepeatPassword == "") {
        $error = "Password And Confirm Password Cannot Be Empty!";
    } else if($_POST['password'] != $_POST['RepeatPassword']) {
        $error = "Password And Confirm Password Do Not Match";
    } else {
        $sql = "INSERT INTO tbluser (fname, lname, email, mobile, state, city, password) VALUES (:fname, :lname, :email, :mobile, :state, :city, :Password)";
        
        $query = $dbh->prepare($sql);
        $query->bindParam(':fname', $fname, PDO::PARAM_STR);
        $query->bindParam(':lname', $lname, PDO::PARAM_STR);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':mobile', $mobile, PDO::PARAM_STR);
        $query->bindParam(':state', $state, PDO::PARAM_STR);
        $query->bindParam(':city', $city, PDO::PARAM_STR);
        $query->bindParam(':Password', $pass, PDO::PARAM_STR);
        
        if($query->execute()) {
            $lastInsertId = $dbh->lastInsertId();
            if($lastInsertId > 0) {
                $succmsg = "Registration successful! Please login to continue.";
                // Send welcome email (best-effort)
                @include_once __DIR__ . '/include/mailer.php';
                $fullName = trim($fname . ' ' . $lname);
                $subject = 'Welcome to FIT TRACK HUB';
                $body = '<div style="font-family:Arial,sans-serif;font-size:14px;line-height:1.6">'
                    .'<h2 style="margin:0 0 10px;color:#111827;">Welcome, '.htmlentities($fullName).'</h2>'
                    .'<p>Thank you for registering at FIT TRACK HUB. Your account has been created successfully.</p>'
                    .'<p>You can now log in and manage your bookings.</p>'
                    .'<p style="margin-top:18px;color:#6b7280;">— FIT TRACK HUB Team</p>'
                    .'</div>';
                if (function_exists('sendMail')) { @sendMail($email, $subject, $body, $fullName); }
                // Clear form data after successful registration
                $fname = $lname = $mobile = $email = $state = $city = "";
            } else {
                $error = "Registration was not successful. Please try again.";
            }
        } else {
            $error = "Database error occurred. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Registration | Elite Fitness</title>
	<meta charset="UTF-8">
	<meta name="description" content="Join Elite Fitness - Create your account and start your fitness journey today">
	<meta name="keywords" content="registration, signup, fitness, gym, membership, account">
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
		
		/* Registration Section */
		.registration-section {
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
		
		/* Registration Form Container */
		.registration-form-container {
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
		
		.registration-form-container::before {
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
		
		.form-control.error {
			border-color: var(--accent-color);
			box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
		}
		
		.form-control.success {
			border-color: #10b981;
			box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
		}
		
		/* Form Row */
		.form-row {
			display: grid;
			grid-template-columns: 1fr 1fr;
			gap: 1.5rem;
		}
		
		@media (max-width: 768px) {
			.form-row {
				grid-template-columns: 1fr;
			}
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
		.btn-register {
			background: var(--gradient-secondary);
			border: none;
			color: white;
			padding: 18px 50px;
			border-radius: 25px;
			font-weight: 600;
			font-size: 1.2rem;
			cursor: pointer;
			transition: all 0.3s ease;
			width: 100%;
			margin-top: 1rem;
		}
		
		.btn-register:hover {
			transform: translateY(-2px);
			box-shadow: 0 10px 30px rgba(240, 147, 251, 0.4);
		}
		
		.btn-register:active {
			transform: translateY(0);
		}
		
		/* Registration Icon */
		.registration-icon {
			text-align: center;
			margin-bottom: 2rem;
		}
		
		.registration-icon i {
			font-size: 4rem;
			color: var(--primary-color);
			opacity: 0.8;
		}
		
		/* Login Link */
		.login-link {
			text-align: center;
			margin-top: 2rem;
			padding-top: 2rem;
			border-top: 1px solid #e5e7eb;
		}
		
		.login-link a {
			color: var(--primary-color);
			text-decoration: none;
			font-weight: 500;
			transition: color 0.3s ease;
		}
		
		.login-link a:hover {
			color: var(--secondary-color);
		}
		
		/* Responsive Design */
		@media (max-width: 768px) {
			.page-title {
				font-size: 2.5rem;
			}
			
			.section-title {
				font-size: 2rem;
			}
			
			.registration-form-container {
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
				<h1 class="page-title">Join Elite Fitness</h1>
				<p class="page-subtitle">Create your account and start your fitness journey today</p>
			</div>
		</div>
	</section>

<!-- Registration Section -->
<!-- Registration Section -->
<section class="registration-section">
  <div class="container">
    <div class="section-header" data-aos="fade-up">
      <h2 class="section-title">Create Account</h2>
      <p class="section-subtitle">Fill in your details to create your Elite Fitness membership</p>
    </div>
    <div class="registration-form-container" data-aos="fade-up" data-aos-delay="200">
      <div class="registration-icon"><i class="fas fa-user-plus"></i></div>
      <h3 class="form-title">Registration Form</h3>
      <form id="registrationForm" class="registration-form" method="post" novalidate>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">First Name</label>
            <input type="text" name="fname" id="fname" class="form-control" placeholder="Enter your first name" pattern="[A-Za-z]+" autocomplete="off" required>
          </div>
          <div class="form-group">
            <label class="form-label">Last Name</label>
            <input type="text" name="lname" id="lname" class="form-control" placeholder="Enter your last name" pattern="[A-Za-z]+" autocomplete="off" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="example@domain.com" autocomplete="off" required>
          </div>
          <div class="form-group">
            <label class="form-label">Mobile Number</label>
            <input type="text" name="mobile" id="mobile" class="form-control" maxlength="10" placeholder="9XXXXXXXXX" pattern="9[0-9]{9}" autocomplete="off" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">State</label>
            <select name="state" id="state" class="form-control" required onchange="updateCities()">
              <option value="">Select Province</option>
              <option value="Koshi">Koshi Province</option>
              <option value="Madhesh">Madhesh Province</option>
              <option value="Bagmati">Bagmati Province</option>
              <option value="Gandaki">Gandaki Province</option>
              <option value="Lumbini">Lumbini Province</option>
              <option value="Karnali">Karnali Province</option>
              <option value="Sudurpashchim">Sudurpashchim Province</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">City</label>
            <select name="city" id="city" class="form-control" required>
              <option value="">Select City</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-control" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" placeholder="Create a strong password" autocomplete="off" required>
          </div>
          <div class="form-group">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="RepeatPassword" id="RepeatPassword" class="form-control" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" placeholder="Confirm your password" autocomplete="off" required>
          </div>
        </div>

        <button type="submit" name="submit" class="btn-register"><i class="fas fa-user-plus me-2"></i>Create Account</button>
      </form>
      <div class="login-link">
        <p>Already have an account? <a href="login.php">Login here</a></p>
      </div>
    </div>
  </div>
</section>

<style>
  .is-invalid { border: 2px solid red; }
  .is-valid { border: 2px solid #28a745; }
</style>

<script>
function updateCities() {
  var cities = {
    Koshi:["Biratnagar","Dharan","Itahari","Bhadrapur"],
    Madhesh:["Janakpur","Birgunj","Kalaiya","Lahan"],
    Bagmati:["Kathmandu","Lalitpur","Bhaktapur","Bharatpur","Hetauda"],
    Gandaki:["Pokhara","Baglung","Gorkha","Besisahar"],
    Lumbini:["Butwal","Bhairahawa","Nepalgunj","Tulsipur"],
    Karnali:["Birendranagar","Jumla","Dailekh"],
    Sudurpashchim:["Dhangadhi","Tikapur","Mahendranagar"]
  };
  var state = document.getElementById("state").value;
  var citySelect = document.getElementById("city");
  citySelect.innerHTML = '<option value="">Select City</option>';
  if(cities[state]){
    cities[state].forEach(function(city){
      var opt = document.createElement("option");
      opt.value = city;
      opt.textContent = city;
      citySelect.appendChild(opt);
    });
  }
}

document.getElementById("registrationForm").addEventListener("submit", function(e){
  var fname = document.getElementById("fname"),
      lname = document.getElementById("lname"),
      email = document.getElementById("email"),
      mobile = document.getElementById("mobile"),
      state = document.getElementById("state"),
      city = document.getElementById("city"),
      password = document.getElementById("password"),
      repeatPassword = document.getElementById("RepeatPassword"),
      emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9-]+\.com$/,
      mobilePattern = /^9\d{9}$/;

  // reset all invalid/valid classes
  [fname,lname,email,mobile,state,city,password,repeatPassword].forEach(f=>f.classList.remove("is-invalid","is-valid"));
  
  var valid = true;

  if(!fname.value.match(/^[A-Za-z]+$/)){ fname.classList.add("is-invalid"); valid = false; }
  if(!lname.value.match(/^[A-Za-z]+$/)){ lname.classList.add("is-invalid"); valid = false; }
  if(!emailPattern.test(email.value)){ email.classList.add("is-invalid"); valid = false; }
  if(!mobilePattern.test(mobile.value)){ mobile.classList.add("is-invalid"); valid = false; }
  if(state.value===""){ state.classList.add("is-invalid"); valid = false; }
  if(city.value===""){ city.classList.add("is-invalid"); valid = false; }
  if(password.value !== repeatPassword.value){ 
    repeatPassword.classList.add("is-invalid");
    valid = false; 
  }

  if(!valid) { e.preventDefault(); }
});

// live password match check
const password = document.getElementById("password");
const repeatPassword = document.getElementById("RepeatPassword");

function validatePasswords() {
  if (!repeatPassword.value) {
    password.classList.remove("is-valid","is-invalid");
    repeatPassword.classList.remove("is-valid","is-invalid");
    return;
  }

  if(password.value === repeatPassword.value) {
    password.classList.add("is-valid");
    password.classList.remove("is-invalid");
    repeatPassword.classList.add("is-valid");
    repeatPassword.classList.remove("is-invalid");
  } else {
    password.classList.remove("is-valid");
    repeatPassword.classList.add("is-invalid");
    repeatPassword.classList.remove("is-valid");
  }
}

password.addEventListener("input", validatePasswords);
repeatPassword.addEventListener("input", validatePasswords);
</script>

</script>
<style>.is-invalid{border:2px solid red!important}.is-valid{border:2px solid #28a745!important}</style>
		

	<!-- Footer Section -->
<?php include 'include/footer.php'; ?>

	<!-- Scripts -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstr	ap.bundle.min.js"></script>
	<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	
	<script>
		// Initialize AOS animations
		AOS.init({
			duration: 1000,
			easing: 'ease-in-out',
			once: true
		});
		
		// Form validation
		function validateForm() {
			const password = document.getElementById('password').value;
			const confirmPassword = document.getElementById('RepeatPassword').value;
			
			if(password !== confirmPassword) {
				alert('Password and Confirm Password fields do not match!');
				document.getElementById('RepeatPassword').focus();
				return false;
			}
			
			if(password.length < 6) {
				alert('Password must be at least 6 characters long!');
				document.getElementById('password').focus();
				return false;
			}
			
			return true;
		}
		
		// Password strength indicator
		document.getElementById('password').addEventListener('input', function() {
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
				
				// Real-time validation
				input.addEventListener('input', function() {
					if(this.checkValidity()) {
						this.classList.remove('error');
						this.classList.add('success');
					} else {
						this.classList.remove('success');
						this.classList.add('error');
					}
				});
			});
		});
	</script>
	<?php if($error || $succmsg) { ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			<?php if($succmsg){ ?>
			Swal.fire({
				icon: "success",
				title: "Success!",
				text: "<?php echo htmlentities($succmsg); ?>",
				timer: 2000,
				showConfirmButton: false
			}).then(() => {
				window.location.href = 'login.php';
			});
			<?php } else { ?>
			Swal.fire({
				icon: "error",
				title: "Oops...",
				text: "<?php echo htmlentities($error); ?>",
			});
			<?php } ?>
		});
	</script>
	<?php } ?>
	</body>
</html>
