<?php session_start();
error_reporting(0);
include 'include/config.php';
if (strlen($_SESSION['adminid']==0)) {
  header('location:logout.php');
} else {

$userid = isset($_GET['userid']) ? intval($_GET['userid']) : 0;

if($userid == 0) {
    header('location:attendance-history.php');
    exit;
}

// Get member info
$sql = "SELECT * FROM tbluser WHERE id=:userid";
$query = $dbh->prepare($sql);
$query->bindParam(':userid', $userid, PDO::PARAM_INT);
$query->execute();
$member = $query->fetch(PDO::FETCH_OBJ);

if(!$member) {
    header('location:attendance-history.php');
    exit;
}

// Get date range (last 30 days by default)
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-d', strtotime('-30 days'));
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');

// Get attendance stats
$sql = "SELECT 
        COUNT(*) as total_sessions,
        SUM(duration_minutes) as total_minutes,
        AVG(duration_minutes) as avg_minutes,
        MIN(check_in_time) as earliest_checkin,
        MAX(check_in_time) as latest_checkin
        FROM tblattendance 
        WHERE userid=:userid 
        AND session_date BETWEEN :from_date AND :to_date 
        AND check_out_time IS NOT NULL";

$query = $dbh->prepare($sql);
$query->bindParam(':userid', $userid, PDO::PARAM_INT);
$query->bindParam(':from_date', $from_date, PDO::PARAM_STR);
$query->bindParam(':to_date', $to_date, PDO::PARAM_STR);
$query->execute();
$stats = $query->fetch(PDO::FETCH_OBJ);

// Calculate participation percentage
$date1 = new DateTime($from_date);
$date2 = new DateTime($to_date);
$date2->modify('+1 day');
$interval = $date1->diff($date2);
$total_days = $interval->days;
$participation_percent = $total_days > 0 ? round(($stats->total_sessions / $total_days) * 100, 2) : 0;

// Get monthly breakdown
$sql = "SELECT 
        DATE_FORMAT(session_date, '%Y-%m') as month,
        COUNT(*) as sessions,
        SUM(duration_minutes) as total_minutes
        FROM tblattendance 
        WHERE userid=:userid 
        AND session_date BETWEEN :from_date AND :to_date
        AND check_out_time IS NOT NULL
        GROUP BY DATE_FORMAT(session_date, '%Y-%m')
        ORDER BY month DESC";

$query = $dbh->prepare($sql);
$query->bindParam(':userid', $userid, PDO::PARAM_INT);
$query->bindParam(':from_date', $from_date, PDO::PARAM_STR);
$query->bindParam(':to_date', $to_date, PDO::PARAM_STR);
$query->execute();
$monthly_stats = $query->fetchAll(PDO::FETCH_OBJ);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin | Member Attendance Report</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .stat-card h3 {
            color: white;
            margin: 0 0 5px 0;
            font-size: 32px;
            font-weight: bold;
        }
        .stat-card p {
            margin: 0;
            opacity: 0.9;
        }
        .member-header {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .member-header h2 {
            margin: 0 0 5px 0;
            color: #333;
        }
        .member-header p {
            margin: 0;
            color: #666;
        }
    </style>
</head>
<body class="app sidebar-mini rtl">
    <?php include 'include/header.php'; ?>
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <?php include 'include/sidebar.php'; ?>
    <main class="app-content">
        <h3><i class="fa fa-bar-chart"></i> Member Attendance Report</h3>
        <hr/>
        
        <div class="row">
            <div class="col-md-12">
                <div class="member-header">
                    <h2><?php echo htmlentities($member->fname); ?></h2>
                    <p><i class="fa fa-envelope"></i> <?php echo htmlentities($member->email); ?></p>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <div class="tile-title-w-btn">
                        <h4 class="title">Date Range Filter</h4>
                    </div>
                    <div class="tile-body">
                        <form method="GET" class="row">
                            <input type="hidden" name="userid" value="<?php echo $userid; ?>">
                            <div class="form-group col-md-4">
                                <label>From Date</label>
                                <input type="date" name="from_date" class="form-control" value="<?php echo $from_date; ?>" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label>To Date</label>
                                <input type="date" name="to_date" class="form-control" value="<?php echo $to_date; ?>" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-filter"></i> Apply Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card">
                    <h3><?php echo $stats->total_sessions ? $stats->total_sessions : 0; ?></h3>
                    <p>Total Sessions</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h3><?php echo $participation_percent; ?>%</h3>
                    <p>Participation Rate</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h3>
                        <?php 
                        if($stats->total_minutes) {
                            echo floor($stats->total_minutes / 60).'h '.($stats->total_minutes % 60).'m';
                        } else {
                            echo '0h 0m';
                        }
                        ?>
                    </h3>
                    <p>Total Time</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h3>
                        <?php 
                        if($stats->avg_minutes) {
                            echo floor($stats->avg_minutes).'m';
                        } else {
                            echo '0m';
                        }
                        ?>
                    </h3>
                    <p>Avg Session Duration</p>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="tile">
                    <h4 class="tile-title">Monthly Breakdown</h4>
                    <div class="tile-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Sessions</th>
                                    <th>Total Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if(count($monthly_stats) > 0) {
                                    foreach($monthly_stats as $row) {
                                        $month_name = date('F Y', strtotime($row->month.'-01'));
                                        $hours = floor($row->total_minutes / 60);
                                        $mins = $row->total_minutes % 60;
                                ?>
                                <tr>
                                    <td><?php echo $month_name; ?></td>
                                    <td><?php echo $row->sessions; ?></td>
                                    <td><?php echo $hours.'h '.$mins.'m'; ?></td>
                                </tr>
                                <?php 
                                    }
                                } else {
                                    echo "<tr><td colspan='3' class='text-center'>No data available</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="tile">
                    <h4 class="tile-title">Recent Sessions</h4>
                    <div class="tile-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM tblattendance 
                                        WHERE userid=:userid 
                                        AND session_date BETWEEN :from_date AND :to_date
                                        ORDER BY session_date DESC, check_in_time DESC 
                                        LIMIT 10";
                                $query = $dbh->prepare($sql);
                                $query->bindParam(':userid', $userid, PDO::PARAM_INT);
                                $query->bindParam(':from_date', $from_date, PDO::PARAM_STR);
                                $query->bindParam(':to_date', $to_date, PDO::PARAM_STR);
                                $query->execute();
                                $sessions = $query->fetchAll(PDO::FETCH_OBJ);
                                
                                if(count($sessions) > 0) {
                                    foreach($sessions as $session) {
                                        $duration = '';
                                        if($session->duration_minutes) {
                                            $h = floor($session->duration_minutes / 60);
                                            $m = $session->duration_minutes % 60;
                                            $duration = ($h > 0 ? $h.'h ' : '').$m.'m';
                                        }
                                ?>
                                <tr>
                                    <td><?php echo date('M d', strtotime($session->session_date)); ?></td>
                                    <td><?php echo date('h:i A', strtotime($session->check_in_time)); ?></td>
                                    <td><?php echo $session->check_out_time ? date('h:i A', strtotime($session->check_out_time)) : '<span class="badge badge-warning">In Session</span>'; ?></td>
                                    <td><?php echo $duration ? $duration : '-'; ?></td>
                                </tr>
                                <?php 
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='text-center'>No sessions found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <a href="attendance-history.php" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Back to History</a>
            </div>
        </div>
    </main>
    
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
<?php } ?>
