<?php
session_start();
error_reporting(0);
require_once('include/config.php');
if(strlen( $_SESSION["adminid"])==0)
    {   
header('location:login.php');
}
else{


if(isset($_POST['submit']))
{
    // Server-side validation
    $adminid = $_SESSION['adminid'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    
    $errors = array();
    
    // Validation rules
    if(empty($name)) {
        $errors[] = "Name is required";
    } elseif(strlen($name) < 2) {
        $errors[] = "Name must be at least 2 characters long";
    } elseif(strlen($name) > 50) {
        $errors[] = "Name cannot exceed 50 characters";
    } elseif(!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $errors[] = "Name can only contain letters and spaces";
    }
    
    if(empty($email)) {
        $errors[] = "Email is required";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }
    
    if(empty($mobile)) {
        $errors[] = "Mobile number is required";
    } elseif(!preg_match("/^[0-9]{10,15}$/", $mobile)) {
        $errors[] = "Please enter a valid mobile number (10-15 digits)";
    }
    
    // If no errors, proceed with update
    if(empty($errors)) {
        $sql = "UPDATE tbladmin SET name=:name, email=:email, mobile=:mobile WHERE id=:adminid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':name', $name, PDO::PARAM_STR);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':mobile', $mobile, PDO::PARAM_STR);
        $query->bindParam(':adminid', $adminid, PDO::PARAM_STR);
        
        if($query->execute()) {
            $msg = "Profile has been updated successfully!";
        } else {
            $errormsg = "Error updating profile. Please try again.";
        }
    } else {
        $errormsg = implode("<br>", $errors);
    }
}


 ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta name="description" content="Vali is a">
   <title>Admin Profile</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  </head>
  <body class="app sidebar-mini rtl">
    <!-- Navbar-->
   <?php include 'include/header.php'; ?>
    <!-- Sidebar menu-->
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <?php include 'include/sidebar.php'; ?>
    <main class="app-content">
      
      <div class="row">
        
        <div class="col-md-12">
          <div class="tile">
            <h3 class="tile-title">Profile</h3>
            
            <!---Success Message--->  
            <?php if(isset($msg) && $msg){ ?>
            <div class="alert alert-success" role="alert">
            <strong>Success!</strong> <?php echo htmlentities($msg);?>
            </div>
            <?php } ?>

            <!---Error Message--->
            <?php if(isset($errormsg) && $errormsg){ ?>
            <div class="alert alert-danger" role="alert">
            <strong>Error!</strong> <?php echo $errormsg;?></div>
            <?php } ?>
            
            <div class="tile-body">
              <form class="row" method="post" id="profileForm" novalidate>
                  <?php 
              $adminid=$_SESSION['adminid'];
              $sql ="SELECT id, name,email,mobile,create_date from tbladmin where id=:adminid ";
              $query= $dbh -> prepare($sql);
              $query->bindParam(':adminid',$adminid, PDO::PARAM_STR);
              $query-> execute();
              $results = $query -> fetchAll(PDO::FETCH_OBJ);
              $cnt=1;
              if($query->rowCount() > 0)
              {
              foreach($results as $result)
              { ?>
                <div class="form-group col-md-12">
                  <label class="control-label">Name</label>
                  <input class="form-control" type="text" name="name" id="name" placeholder="Enter your name" value="<?php echo $result->name;?>">
                </div>
                <div class="form-group col-md-12">
                  <label class="control-label">Email</label>
                  <input class="form-control" type="text" name="email" id="email" placeholder="Enter your email" value="<?php echo $result->email;?>" readonly>
                </div>
                 <div class="form-group col-md-12">
                  <label class="control-label">Mobile No</label>
                  <input class="form-control" type="text" name="mobile" id="mobile" placeholder="Enter your Mobile" value="<?php echo $result->mobile;?>">
                </div>

                         <div class="form-group col-md-12">
                  <label class="control-label">Regd. Date</label>
                  <input class="form-control" type="text" name="reg" id="reg"  value="<?php echo $result->create_date;?>" readonly>
                </div>
                 
                <div class="form-group col-md-4 align-self-end">
                  <input type="submit" id="submit" name="submit" value="Update" class="btn btn-primary">

                </div>
                <?php }} ?>
              </form>
            </div>
          </div>
        </div>
      </div>
    </main>
    <!-- Essential javascripts for application to work-->
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/plugins/pace.min.js"></script>
  
  </body>
</html>
<?php } ?>

  <style>
.errorWrap {
    padding: 10px;
    margin: 0 0 20px 0;
    background: #dd3d36;
    color:#fff;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
.succWrap{
    padding: 10px;
    margin: 0 0 20px 0;
    background: #5cb85c;
    color:#fff;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
        </style>
