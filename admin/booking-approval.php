<?php session_start();
error_reporting(0);
include  'include/config.php'; 
if (!isset($_SESSION['adminid']) || strlen($_SESSION['adminid']) == 0) {
  header('location:logout.php');
  exit();
  } else{

// Handle accept/decline booking actions
$msg = "";
$error = "";

if(isset($_GET['action']) && isset($_GET['bookingid'])) {
    $action = $_GET['action'];
    $bookingid = intval($_GET['bookingid']);
    
    if($bookingid > 0 && ($action == 'accept' || $action == 'decline')) {
        // Check if status column exists
        $hasStatusColumn = false;
        try {
            $checkColSql = "SHOW COLUMNS FROM tblbooking LIKE 'status'";
            $checkColQuery = $dbh->query($checkColSql);
            $hasStatusColumn = ($checkColQuery->rowCount() > 0);
        } catch (PDOException $e) {
            $hasStatusColumn = false;
        }
        
        if($hasStatusColumn) {
            $status = ($action == 'accept') ? 'accepted' : 'declined';
            
            $sql = "UPDATE tblbooking SET status = :status WHERE id = :bookingid";
            $query = $dbh->prepare($sql);
            $query->bindParam(':status', $status, PDO::PARAM_STR);
            $query->bindParam(':bookingid', $bookingid, PDO::PARAM_INT);
            
            if($query->execute()) {
                $msg = "Booking " . ($action == 'accept' ? 'accepted' : 'declined') . " successfully!";
                echo "<script>alert('Booking " . ($action == 'accept' ? 'accepted' : 'declined') . " successfully!');</script>";
                echo "<script>window.location.href='booking-approval.php'</script>";
            } else {
                $error = "Error updating booking status. Please try again.";
            }
        } else {
            // If status column doesn't exist, we need to add it first
            try {
                $alterSql = "ALTER TABLE tblbooking ADD COLUMN status VARCHAR(20) DEFAULT 'pending' AFTER booking_date";
                $dbh->exec($alterSql);
                // Now try the update again
                $status = ($action == 'accept') ? 'accepted' : 'declined';
                $sql = "UPDATE tblbooking SET status = :status WHERE id = :bookingid";
                $query = $dbh->prepare($sql);
                $query->bindParam(':status', $status, PDO::PARAM_STR);
                $query->bindParam(':bookingid', $bookingid, PDO::PARAM_INT);
                
                if($query->execute()) {
                    $msg = "Booking " . ($action == 'accept' ? 'accepted' : 'declined') . " successfully!";
                    echo "<script>alert('Booking " . ($action == 'accept' ? 'accepted' : 'declined') . " successfully!');</script>";
                    echo "<script>window.location.href='booking-approval.php'</script>";
                } else {
                    $error = "Error updating booking status. Please try again.";
                }
            } catch (PDOException $e) {
                $error = "Status column does not exist and could not be created. Please run the SQL script to add it.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta name="description" content="Booking Approval Management">
    <title>Admin | Booking Approval</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
      .status-badge {
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 0.85rem;
        font-weight: 500;
        display: inline-block;
      }
      .status-pending {
        background: #f59e0b;
        color: white;
      }
      .status-accepted {
        background: #10b981;
        color: white;
      }
      .status-declined {
        background: #ef4444;
        color: white;
      }
      .action-buttons {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
      }
      .btn-sm {
        padding: 5px 10px;
        font-size: 0.875rem;
      }
      /* Ensure sidebar is visible */
      .app-sidebar {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
      }
      body.sidenav-toggled .app-sidebar {
        display: block !important;
        visibility: visible !important;
      }
      @media (max-width: 767px) {
        .app-sidebar {
          transform: translateX(0) !important;
        }
      }
    </style>
  </head>
  <body class="app sidebar-mini rtl">
    <!-- Navbar-->
   <?php include 'include/header.php'; ?>
    <!-- Sidebar menu-->
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <?php include 'include/sidebar.php'; ?>
    <script>
      // Ensure sidebar is visible on page load
      document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.querySelector('.app-sidebar');
        const body = document.querySelector('body');
        if (sidebar && body) {
          // Remove any classes that might hide the sidebar
          body.classList.remove('sidenav-toggled');
          sidebar.style.display = 'block';
          sidebar.style.visibility = 'visible';
        }
      });
    </script>
    <main class="app-content">
      <div class="row">
        <div class="col-md-12">
          <div class="tile">
            <div class="tile-body">
              <h3><i class="fa fa-check-circle"></i> Booking Approval Management</h3>
              <p class="text-muted">Review and approve/decline user bookings. Users can only make payment after approval.</p>
              <hr />
              
              <?php if($error){ ?>
                <div class="alert alert-danger">
                  <i class="fa fa-exclamation-triangle"></i> <?php echo htmlentities($error); ?>
                </div>
              <?php } else if($msg){ ?>
                <div class="alert alert-success">
                  <i class="fa fa-check-circle"></i> <?php echo htmlentities($msg); ?>
                </div>
              <?php } ?>
              
              <table class="table table-hover table-bordered" id="sampleTable">
                <thead>
                  <tr>
                    <th>Sr.No</th>
                    <th>Booking ID</th>
                    <th>User Name</th>
                    <th>Email</th>
                    <th>Package</th>
                    <th>Title</th>
                    <th>Price</th>
                    <th>Booking Date</th>
                    <th>Status</th>
                    <th>Payment Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
               <?php
                  // Check if status column exists
                  $hasStatusColumn = false;
                  try {
                      $checkColSql = "SHOW COLUMNS FROM tblbooking LIKE 'status'";
                      $checkColQuery = $dbh->query($checkColSql);
                      $hasStatusColumn = ($checkColQuery->rowCount() > 0);
                  } catch (PDOException $e) {
                      $hasStatusColumn = false;
                  }
                  
                  // Build query based on whether status column exists
                  if($hasStatusColumn) {
                      $sql="SELECT t1.id as bookingid, COALESCE(t1.status, 'pending') as status, t3.fname as Name, t3.email as email, t1.booking_date as bookingdate, t2.titlename as title, t2.PackageDuratiobn as PackageDuratiobn,
t2.Price as Price, t2.Description as Description, t4.category_name as category_name, t5.PackageName as PackageName, t6.paymentType, t6.payment FROM tblbooking as t1
 left join tbladdpackage as t2 on t1.package_id =t2.id
 left join tbluser as t3 on t1.userid=t3.id
 left join tblcategory as t4 on t2.category=t4.id
 left join tblpackage as t5 on t2.PackageType=t5.id
 left join tblpayment as t6 on t1.id=t6.bookingID
 ORDER BY 
   CASE WHEN COALESCE(t1.status, 'pending') = 'pending' THEN 1 
        WHEN COALESCE(t1.status, 'pending') = 'accepted' THEN 2 
        ELSE 3 END,
   t1.booking_date DESC";
                  } else {
                      $sql="SELECT t1.id as bookingid, 'pending' as status, t3.fname as Name, t3.email as email, t1.booking_date as bookingdate, t2.titlename as title, t2.PackageDuratiobn as PackageDuratiobn,
t2.Price as Price, t2.Description as Description, t4.category_name as category_name, t5.PackageName as PackageName, t6.paymentType, t6.payment FROM tblbooking as t1
 left join tbladdpackage as t2 on t1.package_id =t2.id
 left join tbluser as t3 on t1.userid=t3.id
 left join tblcategory as t4 on t2.category=t4.id
 left join tblpackage as t5 on t2.PackageType=t5.id
 left join tblpayment as t6 on t1.id=t6.bookingID
 ORDER BY t1.booking_date DESC";
                  }
                  
                  try {
                      $query= $dbh->prepare($sql);
                      $query-> execute();
                      $results = $query -> fetchAll(PDO::FETCH_OBJ);
                  } catch (PDOException $e) {
                      $error = "Error loading bookings: " . $e->getMessage();
                      $results = [];
                  }
                  
                  $cnt=1;
                  if(count($results) > 0)
                  {
                  foreach($results as $result)
                  {
                  ?>

                <tbody>
                  <tr>
                    <td><?php echo($cnt);?></td>
                    <td><?php echo htmlentities($result->bookingid);?></td>
                    <td><?php echo htmlentities($result->Name);?></td>
                    <td><?php echo htmlentities($result->email);?></td>
                    <td><?php echo htmlentities($result->PackageName);?></td>
                    <td><?php echo htmlentities($result->title);?></td>
                    <td><strong>Rs. <?php echo htmlentities($result->Price);?></strong></td>
                    <td><?php echo htmlentities($result->bookingdate);?></td>
                    <td>
                      <?php 
                        $status = isset($result->status) ? $result->status : 'pending';
                        if($status == 'pending') {
                          echo "<span class='status-badge status-pending'><i class='fa fa-clock'></i> Pending</span>";
                        } elseif($status == 'accepted') {
                          echo "<span class='status-badge status-accepted'><i class='fa fa-check-circle'></i> Accepted</span>";
                        } elseif($status == 'declined') {
                          echo "<span class='status-badge status-declined'><i class='fa fa-times-circle'></i> Declined</span>";
                        }
                      ?>
                    </td>
                    <td>
                      <?php
                        $ptype = isset($result->paymentType) ? trim($result->paymentType) : '';
                        $pamt = isset($result->payment) ? (float)$result->payment : 0.0;
                        if (!$ptype || $pamt <= 0) {
                          echo "<span class='badge badge-warning'>Not Paid</span>";
                        } else {
                          echo "<span class='badge badge-success'>Paid</span>";
                        }
                      ?>
                    </td>
                    <td>
                      <div class="action-buttons">
                        <a href="booking-history-details.php?bookingid=<?php echo htmlentities($result->bookingid);?>">
                          <button class="btn btn-primary btn-sm" type="button" title="View Details">
                            <i class="fa fa-eye"></i> View
                          </button>
                        </a>
                        <?php if($status == 'pending') { ?>
                          <a href="booking-approval.php?action=accept&bookingid=<?php echo htmlentities($result->bookingid);?>" 
                             onclick="return confirm('Are you sure you want to ACCEPT this booking? User will be able to make payment after approval.');">
                            <button class="btn btn-success btn-sm" type="button" title="Accept Booking">
                              <i class="fa fa-check"></i> Accept
                            </button>
                          </a>
                          <a href="booking-approval.php?action=decline&bookingid=<?php echo htmlentities($result->bookingid);?>" 
                             onclick="return confirm('Are you sure you want to DECLINE this booking? This action cannot be undone.');">
                            <button class="btn btn-danger btn-sm" type="button" title="Decline Booking">
                              <i class="fa fa-times"></i> Decline
                            </button>
                          </a>
                        <?php } elseif($status == 'accepted') { ?>
                          <span class="text-success"><i class="fa fa-check-circle"></i> Approved</span>
                        <?php } elseif($status == 'declined') { ?>
                          <span class="text-danger"><i class="fa fa-times-circle"></i> Declined</span>
                        <?php } ?>
                      </div>
                    </td>
                  </tr>
                    <?php  $cnt=$cnt+1; } } else { ?>
                  <tr>
                    <td colspan="11" class="text-center">
                      <p class="text-muted">No bookings found.</p>
                    </td>
                  </tr>
                  <?php } ?>
              
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
    <!-- Page specific javascripts-->
    <!-- Data table plugin-->
    <script type="text/javascript" src="js/plugins/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="js/plugins/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">$('#sampleTable').DataTable();</script>
  
  </body>
</html>
<?php } ?>

