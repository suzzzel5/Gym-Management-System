<?php session_start();
error_reporting(0);
include 'include/config.php';

// Guard: admin session
if (!isset($_SESSION['adminid']) || strlen((string)$_SESSION['adminid']) === 0) {
  header('Location: logout.php');
  exit();
} else {

// Ensure attendance table exists
try {
    $dbh->exec("CREATE TABLE IF NOT EXISTS tblattendance (
        id INT(11) NOT NULL AUTO_INCREMENT,
        userid INT(11) NOT NULL,
        session_date DATE NOT NULL,
        check_in_time DATETIME NOT NULL,
        check_out_time DATETIME DEFAULT NULL,
        duration_minutes INT(11) DEFAULT NULL,
        notes VARCHAR(255) DEFAULT NULL,
        created_by INT(11) DEFAULT NULL,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_user (userid),
        KEY idx_date (session_date),
        KEY idx_checkout (check_out_time)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
} catch (PDOException $e) {
    // Surface a friendly message but do not expose internal error
}

$msg = "";
$error = "";

// Handle Check-in
if(isset($_POST['checkin'])) {
    $userid = isset($_POST['userid']) ? intval($_POST['userid']) : 0;
    $check_in_time = date('Y-m-d H:i:s');
    $session_date = date('Y-m-d');
    $notes = isset($_POST['notes']) ? trim($_POST['notes']) : null;
    
    if ($userid <= 0) {
        $error = "Please select a member.";
    } else {
        // Check if user already checked in today and not checked out
        $check_sql = "SELECT id FROM tblattendance WHERE userid=:userid AND session_date=:session_date AND check_out_time IS NULL";
        $check_query = $dbh->prepare($check_sql);
        $check_query->bindParam(':userid', $userid, PDO::PARAM_INT);
        $check_query->bindParam(':session_date', $session_date, PDO::PARAM_STR);
        $check_query->execute();
        
        if($check_query->rowCount() > 0) {
            $error = "Member already checked in today!";
        } else {
            $sql = "INSERT INTO tblattendance (userid, check_in_time, session_date, notes, created_by) VALUES (:userid, :check_in_time, :session_date, :notes, :admin_id)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':userid', $userid, PDO::PARAM_INT);
            $query->bindParam(':check_in_time', $check_in_time, PDO::PARAM_STR);
            $query->bindParam(':session_date', $session_date, PDO::PARAM_STR);
            $query->bindParam(':notes', $notes, PDO::PARAM_STR);
            $query->bindParam(':admin_id', $_SESSION['adminid'], PDO::PARAM_INT);
            
            if($query->execute()) {
                $msg = "Check-in recorded successfully!";
            } else {
                $error = "Error recording check-in.";
            }
        }
    }
}

// Handle Check-out
if(isset($_POST['checkout'])) {
    $attendance_id = isset($_POST['attendance_id']) ? intval($_POST['attendance_id']) : 0;
    if ($attendance_id > 0) {
        $check_out_time = date('Y-m-d H:i:s');
        
        // Get check-in time to calculate duration
        $get_sql = "SELECT check_in_time FROM tblattendance WHERE id=:id";
        $get_query = $dbh->prepare($get_sql);
        $get_query->bindParam(':id', $attendance_id, PDO::PARAM_INT);
        $get_query->execute();
        $result = $get_query->fetch(PDO::FETCH_OBJ);
        
        if($result) {
            $check_in = new DateTime($result->check_in_time);
            $check_out = new DateTime($check_out_time);
            $duration = $check_out->diff($check_in);
            $duration_minutes = ($duration->days * 24 * 60) + ($duration->h * 60) + $duration->i;
            
            $sql = "UPDATE tblattendance SET check_out_time=:check_out_time, duration_minutes=:duration WHERE id=:id";
            $query = $dbh->prepare($sql);
            $query->bindParam(':check_out_time', $check_out_time, PDO::PARAM_STR);
            $query->bindParam(':duration', $duration_minutes, PDO::PARAM_INT);
            $query->bindParam(':id', $attendance_id, PDO::PARAM_INT);
            
            if($query->execute()) {
                $msg = "Check-out recorded successfully!";
            } else {
                $error = "Error recording check-out.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin | Attendance Check-in</title>
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
        <h3><i class="fa fa-sign-in"></i> Member Check-in/Check-out</h3>
        <hr/>
        
        <?php if($msg){ ?>
        <div class="alert alert-success" role="alert">
            <strong>Success!</strong> <?php echo htmlentities($msg);?>
        </div>
        <?php } ?>
        
        <?php if($error){ ?>
        <div class="alert alert-danger" role="alert">
            <strong>Error!</strong> <?php echo htmlentities($error);?>
        </div>
        <?php } ?>
        
        <div class="row">
            <!-- Check-in Form -->
            <div class="col-md-6">
                <div class="tile">
                    <h4 class="tile-title"><i class="fa fa-sign-in"></i> Check-in Member</h4>
                    <div class="tile-body">
                        <form method="post">
                            <div class="form-group">
                                <label>Select Member</label>
                                <select name="userid" class="form-control" required>
                                    <option value="">-- Select Member --</option>
                                    <?php
                                    $sql = "SELECT id, fname, email FROM tbluser ORDER BY fname";
                                    $query = $dbh->prepare($sql);
                                    $query->execute();
                                    $users = $query->fetchAll(PDO::FETCH_OBJ);
                                    foreach($users as $user) {
                                        echo "<option value='".$user->id."'>".$user->fname." (".$user->email.")</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Notes (Optional)</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Any notes about this session..."></textarea>
                            </div>
                            <button type="submit" name="checkin" class="btn btn-success btn-block">
                                <i class="fa fa-sign-in"></i> Check-in Now
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Active Sessions -->
            <div class="col-md-6">
                <div class="tile">
                    <h4 class="tile-title"><i class="fa fa-users"></i> Active Sessions Today</h4>
                    <div class="tile-body">
                        <?php
                        $today = date('Y-m-d');
                        $sql = "SELECT a.id, a.check_in_time, u.fname, u.email 
                                FROM tblattendance a 
                                JOIN tbluser u ON a.userid = u.id 
                                WHERE a.session_date = :today AND a.check_out_time IS NULL 
                                ORDER BY a.check_in_time DESC";
                        $query = $dbh->prepare($sql);
                        $query->bindParam(':today', $today, PDO::PARAM_STR);
                        $query->execute();
                        $active_sessions = $query->fetchAll(PDO::FETCH_OBJ);
                        
                        if($query->rowCount() > 0) {
                            foreach($active_sessions as $session) {
                        ?>
                        <div class="alert alert-info">
                            <strong><?php echo htmlentities($session->fname); ?></strong><br>
                            <small>Checked in at: <?php echo date('h:i A', strtotime($session->check_in_time)); ?></small>
                            <form method="post" style="display:inline; float:right;">
                                <input type="hidden" name="attendance_id" value="<?php echo $session->id; ?>">
                                <button type="submit" name="checkout" class="btn btn-sm btn-danger">
                                    <i class="fa fa-sign-out"></i> Check-out
                                </button>
                            </form>
                        </div>
                        <?php 
                            }
                        } else {
                            echo "<p class='text-muted'>No active sessions right now.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Today's Attendance Summary -->
        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <h4 class="tile-title"><i class="fa fa-calendar"></i> Today's Attendance</h4>
                    <div class="tile-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Duration</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT a.*, u.fname, u.email 
                                        FROM tblattendance a 
                                        JOIN tbluser u ON a.userid = u.id 
                                        WHERE a.session_date = :today 
                                        ORDER BY a.check_in_time DESC";
                                $query = $dbh->prepare($sql);
                                $query->bindParam(':today', $today, PDO::PARAM_STR);
                                $query->execute();
                                $todays_attendance = $query->fetchAll(PDO::FETCH_OBJ);
                                
                                if($query->rowCount() > 0) {
                                    foreach($todays_attendance as $att) {
                                ?>
                                <tr>
                                    <td><?php echo htmlentities($att->fname); ?></td>
                                    <td><?php echo date('h:i A', strtotime($att->check_in_time)); ?></td>
                                    <td><?php echo $att->check_out_time ? date('h:i A', strtotime($att->check_out_time)) : '<span class="badge badge-warning">Active</span>'; ?></td>
                                    <td><?php echo $att->duration_minutes ? $att->duration_minutes.' mins' : '-'; ?></td>
                                    <td><?php echo htmlentities($att->notes); ?></td>
                                </tr>
                                <?php 
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center'>No attendance records for today.</td></tr>";
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
</body>
</html>
<?php } ?>
