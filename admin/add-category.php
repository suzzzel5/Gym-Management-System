<?php session_start();
error_reporting(0);
include  'include/config.php'; 
if (strlen($_SESSION['adminid']==0)) {
  header('location:logout.php');
  } else{

if(isset($_POST['submit'])){
    // Server-side validation
    $category = trim($_POST['category']);
    $errors = array();
    
    // Validation rules
    if(empty($category)) {
        $errors[] = "Category name is required";
    } elseif(strlen($category) < 2) {
        $errors[] = "Category name must be at least 2 characters long";
    } elseif(strlen($category) > 50) {
        $errors[] = "Category name cannot exceed 50 characters";
    } elseif(!preg_match("/^[a-zA-Z0-9\s\-_]+$/", $category)) {
        $errors[] = "Category name can only contain letters, numbers, spaces, hyphens and underscores";
    }
    
    // Check if category already exists
    if(empty($errors)) {
        $check_sql = "SELECT id FROM tblcategory WHERE category_name = :category";
        $check_query = $dbh->prepare($check_sql);
        $check_query->bindParam(':category', $category, PDO::PARAM_STR);
        $check_query->execute();
        
        if($check_query->rowCount() > 0) {
            $errors[] = "Category already exists";
        }
    }
    
    // If no errors, proceed with insertion
    if(empty($errors)) {
        $sql="INSERT INTO tblcategory (category_name) Values(:category)";
        $query = $dbh -> prepare($sql);
        $query->bindParam(':category',$category,PDO::PARAM_STR);
        
        if($query -> execute()) {
            $msg = "Category Added Successfully";
            // Clear the form
            $_POST['category'] = '';
        } else {
            $errormsg = "Error adding category. Please try again.";
        }
    } else {
        $errormsg = implode("<br>", $errors);
    }
}

//Delete Record Data
if(isset($_REQUEST['del']))
{
    $uid=intval($_GET['del']);
    
    // Check if category is being used in packages
    $check_usage = "SELECT COUNT(*) as count FROM tblpackage WHERE cate_id = :id";
    $usage_query = $dbh->prepare($check_usage);
    $usage_query->bindParam(':id', $uid, PDO::PARAM_STR);
    $usage_query->execute();
    $usage_result = $usage_query->fetch(PDO::FETCH_ASSOC);
    
    if($usage_result['count'] > 0) {
        echo "<script>alert('Cannot delete category. It is being used by packages.');</script>";
    } else {
        $sql = "DELETE FROM tblcategory WHERE id=:id";
        $query = $dbh->prepare($sql);
        $query-> bindParam(':id',$uid, PDO::PARAM_STR);
        
        if($query -> execute()) {
            echo "<script>alert('Category deleted successfully');</script>";
        } else {
            echo "<script>alert('Error deleting category');</script>";
        }
    }
    echo "<script>window.location.href='add-category.php'</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta name="description" content="Admin Categories Management">
   <title>Admin | Categories</title>
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
     <h3>Categories</h3>
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

           
            <div class="tile-body">
              <form method="post" id="categoryForm" novalidate>
                <div class="form-group col-md-12">
                  <label class="control-label">Category Name <span class="text-danger">*</span></label>
                  <input class="form-control" name="category" id="category" type="text" 
                         placeholder="Enter category name" 
                         value="<?php echo isset($_POST['category']) ? htmlentities($_POST['category']) : ''; ?>"
                         maxlength="50" required>
                  <div class="validation-error" id="category-error"></div>
                </div>
                <div class="form-group col-md-4 align-self-end">
                  <button type="submit" name="submit" id="submit" class="btn btn-primary" onclick="return validateForm()">
                    <i class="fa fa-plus"></i> Add Category
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
                    <th>Category Name</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $sql = "SELECT * FROM tblcategory ORDER BY category_name";
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
                    <td>
                      <a href="add-category.php?del=<?php echo htmlentities($result->id);?>" 
                         onclick="return confirm('Are you sure you want to delete this category?')" 
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
            const categoryError = document.getElementById('category-error');
            
            // Reset previous validation states
            category.classList.remove('error-field', 'success-field');
            categoryError.style.display = 'none';
            
            // Validate category name
            if(category.value.trim() === '') {
                category.classList.add('error-field');
                categoryError.textContent = 'Category name is required';
                categoryError.style.display = 'block';
                isValid = false;
            } else if(category.value.trim().length < 2) {
                category.classList.add('error-field');
                categoryError.textContent = 'Category name must be at least 2 characters long';
                categoryError.style.display = 'block';
                isValid = false;
            } else if(category.value.trim().length > 50) {
                category.classList.add('error-field');
                categoryError.textContent = 'Category name cannot exceed 50 characters';
                categoryError.style.display = 'block';
                isValid = false;
            } else if(!/^[a-zA-Z0-9\s\-_]+$/.test(category.value.trim())) {
                category.classList.add('error-field');
                categoryError.textContent = 'Category name can only contain letters, numbers, spaces, hyphens and underscores';
                categoryError.style.display = 'block';
                isValid = false;
            } else {
                category.classList.add('success-field');
            }
            
            return isValid;
        }
        
        // Real-time validation on input
        document.getElementById('category').addEventListener('input', function() {
            const category = this;
            const categoryError = document.getElementById('category-error');
            
            if(category.value.trim() !== '') {
                category.classList.remove('error-field');
                categoryError.style.display = 'none';
                
                if(category.value.trim().length >= 2 && category.value.trim().length <= 50 && /^[a-zA-Z0-9\s\-_]+$/.test(category.value.trim())) {
                    category.classList.add('success-field');
                } else {
                    category.classList.remove('success-field');
                }
            } else {
                category.classList.remove('error-field', 'success-field');
                categoryError.style.display = 'none';
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