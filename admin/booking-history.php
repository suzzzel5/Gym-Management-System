<?php session_start();
error_reporting(0);
include  'include/config.php'; 
if (strlen($_SESSION['adminid']==0)) {
  header('location:logout.php');
  } else{

// Handle delete request
if(isset($_GET['del'])) {
    $bookingid = $_GET['del'];
    
    // Delete payment records first (if any)
    $sql_payment = "DELETE FROM tblpayment WHERE bookingID=:bookingid";
    $query_payment = $dbh->prepare($sql_payment);
    $query_payment->bindParam(':bookingid', $bookingid, PDO::PARAM_INT);
    $query_payment->execute();
    
    // Delete booking
    $sql = "DELETE FROM tblbooking WHERE id=:bookingid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':bookingid', $bookingid, PDO::PARAM_INT);
    $query->execute();
    
    echo "<script>alert('Booking deleted successfully');</script>";
    echo "<script>window.location.href='booking-history.php'</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta name="description" content="Vali is a">
   <title>admin | All Bookings</title>
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
            <div class="tile-body">
              <h3>All Bookings</h3>
              <hr />
              <table class="table table-hover table-bordered" id="sampleTable">
                <thead>
                  <tr>
                    <th>Sr.No</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Category</th>
                    <th>Package</th>
                    <th>Title</th>
                    <th>Booking Date</th>
                    <th>Payment Type</th>
                    <th>Payment Status</th>
                    <th>Action</th>
                    
                  </tr>
                </thead>
               <?php
                  $sql = "SELECT t1.id as bookingid, t3.fname as Name, t3.email as email, t1.booking_date as bookingdate, t2.titlename as title, t2.PackageDuratiobn as PackageDuratiobn, t2.Price as Price, t2.Description as Description, t4.category_name as category_name, t5.PackageName as PackageName, t6.paymentType, t6.payment FROM tblbooking as t1 left join tbladdpackage as t2 on t1.package_id = t2.id left join tbluser as t3 on t1.userid = t3.id left join tblcategory as t4 on t2.category = t4.id left join tblpackage as t5 on t2.PackageType = t5.id left join tblpayment as t6 on t1.id = t6.bookingID ORDER BY t1.booking_date DESC";
                  $query= $dbh->prepare($sql);
                  $query-> execute();
                  $results = $query -> fetchAll(PDO::FETCH_OBJ);
                  $cnt=1;
                  if($query -> rowCount() > 0)
                  {
                  foreach($results as $result)
                  {
                  ?>

                <tbody>
                  <tr>
                    <td><?php echo($cnt);?></td>
                    <td><?php echo htmlentities($result->Name);?></td>
                    <td><?php echo htmlentities($result->email);?></td>
                    <td><?php echo htmlentities($result->category_name);?></td>
                    <td><?php echo htmlentities($result->PackageName);?></td>
                    <td><?php echo htmlentities($result->title);?></td>
                    <td><?php echo htmlentities($result->bookingdate);?></td>
                    <td><?php echo htmlentities($result->paymentType ? $result->paymentType : 'Not Paid'); ?></td>
                    <td>
                      <?php
                        $ptype = isset($result->paymentType) ? trim($result->paymentType) : '';
                        $pamt = isset($result->payment) ? (float)$result->payment : 0.0;
                        if (!$ptype) {
                          echo "<span class='badge badge-danger'>Pending</span>";
                        } else if (stripos($ptype, 'pending') !== false || $pamt <= 0) {
                          echo "<span class='badge badge-warning'>Pending</span>";
                        } else {
                          echo "<span class='badge badge-success'>Paid</span>";
                        }
                      ?>
                    </td>
                    <td>
                      <a href="booking-history-details.php?bookingid=<?php echo htmlentities($result->bookingid);?>"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-eye"></i> View</button></a>
                      <a href="booking-history.php?del=<?php echo htmlentities($result->bookingid);?>" onclick="return confirm('Are you sure you want to delete this booking?');"><button class="btn btn-danger btn-sm" type="button"><i class="fa fa-trash"></i> Delete</button></a>
                    </td>
                  </tr>
                    <?php  $cnt=$cnt+1; } } ?>
              
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
