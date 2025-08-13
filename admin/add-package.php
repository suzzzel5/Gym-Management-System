<?php session_start();
error_reporting(0);
include  'include/config.php'; 
if (strlen($_SESSION['adminid']==0)) {
  header('location:logout.php');
  } else{

if(isset($_POST['submit'])){
    // Server-side validation
    $AddPackage = trim($_POST['addPackage']);
    $category = $_POST['category'];
    $errors = array();
    
    // Validation rules
    if($category == 'NA' || empty($category)) {
        $errors[] = "Please select a category";
    }
    
    if(empty($AddPackage)) {
        $errors[] = "Package name is required";
    } elseif(strlen($AddPackage) < 2) {
        $errors[] = "Package name must be at least 2 characters long";
    } elseif(strlen($AddPackage) > 100) {
        $errors[] = "Package name cannot exceed 100 characters";
    } elseif(!preg_match("/^[a-zA-Z0-9\s\-_]+$/", $AddPackage)) {
        $errors[] = "Package name can only contain letters, numbers, spaces, hyphens and underscores";
    }
    
    // Check if package already exists in the same category
    if(empty($errors)) {
        $check_sql = "SELECT id FROM tblpackage WHERE PackageName = :package AND cate_id = :category";
        $check_query = $dbh->prepare($check_sql);
        $check_query->bindParam(':package', $AddPackage, PDO::PARAM_STR);
        $check_query->bindParam(':category', $category, PDO::PARAM_STR);
        $check_query->execute();
        
        if($check_query->rowCount() > 0) {
            $errors[] = "Package already exists in this category";
        }
    }
    
    // If no errors, proceed with insertion
    if(empty($errors)) {
        $sql="INSERT INTO tblpackage (PackageName,cate_id) Values(:Package,:category)";
        $query = $dbh -> prepare($sql);
        $query->bindParam(':Package',$AddPackage,PDO::PARAM_STR);
        $query->bindParam(':category',$category,PDO::PARAM_STR);
        
        if($query -> execute()) {
            $msg = "Package Added Successfully";
            // Clear the form
            $_POST['addPackage'] = '';
            $_POST['category'] = 'NA';
        } else {
            $errormsg = "Error adding package. Please try again.";
        }
    } else {
        $errormsg = implode("<br>", $errors);
    }
}

//Delete Record Data
if(isset($_REQUEST['del']))
{
    $uid=intval($_GET['del']);
    
    // Check if package is being used in addpackage table
    $check_usage = "SELECT COUNT(*) as count FROM tbladdpackage WHERE PackageType = :id";
    $usage_query = $dbh->prepare($check_usage);
    $usage_query->bindParam(':id', $uid, PDO::PARAM_STR);
    $usage_query->execute();
    $usage_result = $usage_query->fetch(PDO::FETCH_ASSOC);
    
    if($usage_result['count'] > 0) {
        echo "<script>alert('Cannot delete package. It is being used by packages.');</script>";
    } else {
        $sql = "DELETE FROM tblpackage WHERE id=:id";
        $query = $dbh->prepare($sql);
        $query-> bindParam(':id',$uid, PDO::PARAM_STR);
        
        if($query -> execute()) {
            echo "<script>alert('Package deleted successfully');</script>";
        } else {
            echo "<script>alert('Error deleting package');</script>";
        }
    }
    echo "<script>window.location.href='add-package.php'</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta name="description" content="Admin Package Types Management">
   <title>Admin | Add Package Type</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .error-field {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }
        .success-field {
            border-color: #28a745 !important;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25) !important;
        }
        .validation-error {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }
    </style>
  </head>
  <body class="app sidebar-mini rtl">
    <!-- Navbar-->
   <?php include 'include/header.php'; ?>
    <!-- Sidebar menu-->
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <?php include 'include/sidebar.php'; ?>
    <main class="app-content">
     <h3>Package Types</h3>
     <hr />
      <div class="row">
        
        <div class="col-md-6">
          <div class="tile">
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

              <form class="row" method="post" id="packageForm" novalidate>
                 <div class="form-group col-md-12">
                  <label class="control-label">Category <span class="text-danger">*</span></label>
                  <select class="form-control" name="category" id="category" required>
                  <option value="NA">-- Select Category --</option>
                  <?php 
                  $stmt = $dbh->prepare("SELECT * FROM tblcategory ORDER BY category_name");
                  $stmt->execute();
                  $countriesList = $stmt->fetchAll();
                  foreach($countriesList as $country){
                  $selected = (isset($_POST['category']) && $_POST['category'] == $country['id']) ? 'selected' : '';
                  echo "<option value='".$country['id']."' ".$selected.">".$country['category_name']."</option>";
                  }
                  ?>
                  </select>
                  <div class="validation-error" id="category-error"></div>
                </div>

               

                <div class="form-group col-md-12">
                  <label class="control-label">Package Name <span class="text-danger">*</span></label>
                  <input class="form-control" name="addPackage" id="addPackage" type="text" 
                         placeholder="Enter package name" 
                         value="<?php echo isset($_POST['addPackage']) ? htmlentities($_POST['addPackage']) : ''; ?>"
                         maxlength="100" required>
                  <div class="validation-error" id="package-error"></div>
                </div>

                <div class="form-group col-md-4 align-self-end">
                  <button type="submit" name="submit" id="submit" class="btn btn-primary" onclick="return validateForm()">
                    <i class="fa fa-plus"></i> Add Package
                  </button>
                </div>
              </form>
              
            </div>
          </div>
        </div>
      </div>
       <div class="row">
        <div class="col-md-12">
          <div class="tile">
            <div class="tile-body">
              <table class="table table-hover table-bordered" id="sampleTable">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Category</th>
                    <th>Package Name</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $sql = "SELECT p.*, c.category_name FROM tblpackage p 
                          INNER JOIN tblcategory c ON p.cate_id = c.id 
                          ORDER BY c.category_name, p.PackageName";
                  $query = $dbh->prepare($sql);
                  $query->execute();
                  $results = $query->fetchAll(PDO::FETCH_OBJ);
                  $cnt = 1;
                  if($query->rowCount() > 0) {
                    foreach($results as $result) {
                  ?>
                  <tr>
                    <td><?php echo htmlentities($cnt);?></td>
                    <td><?php echo htmlentities($result->category_name);?></td>
                    <td><?php echo htmlentities($result->PackageName);?></td>
                    <td>
                      <a href="add-package.php?del=<?php echo htmlentities($result->id);?>" 
                         onclick="return confirm('Are you sure you want to delete this package?')" 
                         class="btn btn-danger btn-sm">
                        <i class="fa fa-trash"></i> Delete
                      </a>
                    </td>
                  </tr>
                  <?php $cnt++; } } ?>
                </tbody>
              </table>
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
    <!-- The javascript plugin to display page loading on top-->
    <script src="js/plugins/pace.min.js"></script>
    <!-- Data table plugin-->
    <script type="text/javascript" src="js/plugins/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="js/plugins/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">$('#sampleTable').DataTable();</script>
    
    <script>
        // Client-side validation
        function validateForm() {
            let isValid = true;
            const category = document.getElementById('category');
            const package = document.getElementById('addPackage');
            const categoryError = document.getElementById('category-error');
            const packageError = document.getElementById('package-error');
            
            // Reset previous validation states
            category.classList.remove('error-field', 'success-field');
            package.classList.remove('error-field', 'success-field');
            categoryError.style.display = 'none';
            packageError.style.display = 'none';
            
            // Validate category
            if(category.value === 'NA' || category.value === '') {
                category.classList.add('error-field');
                categoryError.textContent = 'Please select a category';
                categoryError.style.display = 'block';
                isValid = false;
            } else {
                category.classList.add('success-field');
            }
            
            // Validate package name
            if(package.value.trim() === '') {
                package.classList.add('error-field');
                packageError.textContent = 'Package name is required';
                packageError.style.display = 'block';
                isValid = false;
            } else if(package.value.trim().length < 2) {
                package.classList.add('error-field');
                packageError.textContent = 'Package name must be at least 2 characters long';
                packageError.style.display = 'block';
                isValid = false;
            } else if(package.value.trim().length > 100) {
                package.classList.add('error-field');
                packageError.textContent = 'Package name cannot exceed 100 characters';
                packageError.style.display = 'block';
                isValid = false;
            } else if(!/^[a-zA-Z0-9\s\-_]+$/.test(package.value.trim())) {
                package.classList.add('error-field');
                packageError.textContent = 'Package name can only contain letters, numbers, spaces, hyphens and underscores';
                packageError.style.display = 'block';
                isValid = false;
            } else {
                package.classList.add('success-field');
            }
            
            return isValid;
        }
        
        // Real-time validation on input
        document.getElementById('addPackage').addEventListener('input', function() {
            const package = this;
            const packageError = document.getElementById('package-error');
            
            if(package.value.trim() !== '') {
                package.classList.remove('error-field');
                packageError.style.display = 'none';
                
                if(package.value.trim().length >= 2 && package.value.trim().length <= 100 && /^[a-zA-Z0-9\s\-_]+$/.test(package.value.trim())) {
                    package.classList.add('success-field');
                } else {
                    package.classList.remove('success-field');
                }
            } else {
                package.classList.remove('error-field', 'success-field');
                packageError.style.display = 'none';
            }
        });
        
        document.getElementById('category').addEventListener('change', function() {
            const category = this;
            const categoryError = document.getElementById('category-error');
            
            if(category.value !== 'NA' && category.value !== '') {
                category.classList.remove('error-field');
                category.classList.add('success-field');
                categoryError.style.display = 'none';
            } else {
                category.classList.remove('success-field');
            }
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.display = 'none';
            });
        }, 5000);
    </script>
    
  </body>
</html>
<?php } ?>