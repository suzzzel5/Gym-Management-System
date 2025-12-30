<?php session_start();
error_reporting(0);
include 'include/config.php';
if (strlen($_SESSION['adminid']==0)) {
  header('location:logout.php');
} else {

// Get filter parameters
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-01');
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');
$search = isset($_GET['search']) ? $_GET['search'] : '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin | Attendance History</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body class="app sidebar-mini rtl">
    <?php include 'include/header.php'; ?>
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <?php include 'include/sidebar.php'; ?>
    <main class="app-content">
        <h3><i class="fa fa-history"></i> Attendance History</h3>
        <hr/>
        
        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <div class="tile-title-w-btn">
                        <h4 class="title">Filter Attendance Records</h4>
                    </div>
                    <div class="tile-body">
                        <form method="GET" class="row">
                            <div class="form-group col-md-3">
                                <label>From Date</label>
                                <input type="date" name="from_date" class="form-control" value="<?php echo $from_date; ?>" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label>To Date</label>
                                <input type="date" name="to_date" class="form-control" value="<?php echo $to_date; ?>" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Search Member</label>
                                <input type="text" name="search" class="form-control" placeholder="Member name or email" value="<?php echo htmlentities($search); ?>">
                            </div>
                            <div class="form-group col-md-2">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-search"></i> Filter</button>
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
                                    <th>Sr.No</th>
                                    <th>Member</th>
                                    <th>Email</th>
                                    <th>Date</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Duration</th>
                                    <th>Notes</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT a.*, u.fname, u.email 
                                        FROM tblattendance a 
                                        JOIN tbluser u ON a.userid = u.id 
                                        WHERE a.session_date BETWEEN :from_date AND :to_date";
                                
                                if(!empty($search)) {
                                    $sql .= " AND (u.fname LIKE :search OR u.email LIKE :search)";
                                }
                                
                                $sql .= " ORDER BY a.session_date DESC, a.check_in_time DESC";
                                
                                $query = $dbh->prepare($sql);
                                $query->bindParam(':from_date', $from_date, PDO::PARAM_STR);
                                $query->bindParam(':to_date', $to_date, PDO::PARAM_STR);
                                
                                if(!empty($search)) {
                                    $search_param = "%".$search."%";
                                    $query->bindParam(':search', $search_param, PDO::PARAM_STR);
                                }
                                
                                $query->execute();
                                $results = $query->fetchAll(PDO::FETCH_OBJ);
                                $cnt = 1;
                                
                                if($query->rowCount() > 0) {
                                    foreach($results as $result) {
                                ?>
                                <tr>
                                    <td><?php echo $cnt; ?></td>
                                    <td><?php echo htmlentities($result->fname); ?></td>
                                    <td><?php echo htmlentities($result->email); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($result->session_date)); ?></td>
                                    <td><?php echo date('h:i A', strtotime($result->check_in_time)); ?></td>
                                    <td>
                                        <?php 
                                        if($result->check_out_time) {
                                            echo date('h:i A', strtotime($result->check_out_time));
                                        } else {
                                            echo '<span class="badge badge-warning">In Session</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        if($result->duration_minutes) {
                                            $hours = floor($result->duration_minutes / 60);
                                            $mins = $result->duration_minutes % 60;
                                            echo $hours > 0 ? $hours.'h ' : '';
                                            echo $mins.'m';
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo htmlentities($result->notes); ?></td>
                                    <td>
                                        <a href="attendance-report.php?userid=<?php echo $result->userid; ?>" class="btn btn-sm btn-info">
                                            <i class="fa fa-bar-chart"></i> Report
                                        </a>
                                    </td>
                                </tr>
                                <?php 
                                        $cnt++;
                                    }
                                } else {
                                    echo "<tr><td colspan='9' class='text-center'>No attendance records found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
    <script type="text/javascript" src="js/plugins/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="js/plugins/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">$('#sampleTable').DataTable();</script>
</body>
</html>
<?php } ?>
