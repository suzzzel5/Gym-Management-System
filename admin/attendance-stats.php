<?php session_start();
error_reporting(0);
include 'include/config.php';
if (strlen($_SESSION['adminid']==0)) {
  header('location:logout.php');
} else {

// Get overall statistics
$sql = "SELECT 
        COUNT(DISTINCT userid) as total_members,
        COUNT(*) as total_sessions,
        SUM(duration_minutes) as total_minutes,
        AVG(duration_minutes) as avg_duration
        FROM tblattendance
        WHERE check_out_time IS NOT NULL";
$query = $dbh->query($sql);
$overall_stats = $query->fetch(PDO::FETCH_OBJ);

// Get today's stats
$sql = "SELECT 
        COUNT(DISTINCT userid) as today_members,
        COUNT(*) as today_sessions
        FROM tblattendance
        WHERE session_date = CURDATE()";
$query = $dbh->query($sql);
$today_stats = $query->fetch(PDO::FETCH_OBJ);

// Get currently checked in members
$sql = "SELECT COUNT(*) as active_members
        FROM tblattendance
        WHERE session_date = CURDATE() AND check_out_time IS NULL";
$query = $dbh->query($sql);
$active = $query->fetch(PDO::FETCH_OBJ);

// Get top 10 most active members
$sql = "SELECT 
        u.fname, u.email,
        COUNT(*) as total_sessions,
        SUM(a.duration_minutes) as total_minutes
        FROM tblattendance a
        JOIN tbluser u ON a.userid = u.id
        WHERE a.check_out_time IS NOT NULL
        GROUP BY a.userid
        ORDER BY total_sessions DESC
        LIMIT 10";
$query = $dbh->query($sql);
$top_members = $query->fetchAll(PDO::FETCH_OBJ);

// Get last 7 days attendance data for chart
$sql = "SELECT 
        session_date,
        COUNT(DISTINCT userid) as unique_members,
        COUNT(*) as total_sessions
        FROM tblattendance
        WHERE session_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY session_date
        ORDER BY session_date ASC";
$query = $dbh->query($sql);
$weekly_data = $query->fetchAll(PDO::FETCH_OBJ);

// Prepare chart data
$chart_dates = [];
$chart_members = [];
$chart_sessions = [];
foreach($weekly_data as $day) {
    $chart_dates[] = date('M d', strtotime($day->session_date));
    $chart_members[] = $day->unique_members;
    $chart_sessions[] = $day->total_sessions;
}

// Get monthly comparison
$sql = "SELECT 
        DATE_FORMAT(session_date, '%Y-%m') as month,
        COUNT(DISTINCT userid) as unique_members,
        COUNT(*) as total_sessions,
        SUM(duration_minutes) as total_minutes
        FROM tblattendance
        WHERE session_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        AND check_out_time IS NOT NULL
        GROUP BY DATE_FORMAT(session_date, '%Y-%m')
        ORDER BY month DESC";
$query = $dbh->query($sql);
$monthly_data = $query->fetchAll(PDO::FETCH_OBJ);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin | Attendance Statistics</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stat-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .stat-box h2 {
            margin: 0;
            font-size: 42px;
            font-weight: bold;
            color: white;
        }
        .stat-box p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .stat-box.success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        .stat-box.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .stat-box.info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="app sidebar-mini rtl">
    <?php include 'include/header.php'; ?>
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <?php include 'include/sidebar.php'; ?>
    <main class="app-content">
        <h3><i class="fa fa-line-chart"></i> Attendance Statistics Dashboard</h3>
        <hr/>
        
        <!-- Overview Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="stat-box">
                    <h2><?php echo $overall_stats->total_sessions ? $overall_stats->total_sessions : 0; ?></h2>
                    <p><i class="fa fa-calendar-check-o"></i> Total Sessions</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box success">
                    <h2><?php echo $overall_stats->total_members ? $overall_stats->total_members : 0; ?></h2>
                    <p><i class="fa fa-users"></i> Active Members</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box warning">
                    <h2><?php echo $active->active_members ? $active->active_members : 0; ?></h2>
                    <p><i class="fa fa-clock-o"></i> Currently Checked In</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box info">
                    <h2><?php echo $today_stats->today_sessions ? $today_stats->today_sessions : 0; ?></h2>
                    <p><i class="fa fa-calendar"></i> Today's Sessions</p>
                </div>
            </div>
        </div>
        
        <!-- Weekly Attendance Chart -->
        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <h4 class="tile-title"><i class="fa fa-area-chart"></i> Last 7 Days Attendance</h4>
                    <div class="tile-body">
                        <div class="chart-container">
                            <canvas id="weeklyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Top Members and Monthly Stats -->
        <div class="row">
            <div class="col-md-6">
                <div class="tile">
                    <h4 class="tile-title"><i class="fa fa-trophy"></i> Top 10 Most Active Members</h4>
                    <div class="tile-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Member</th>
                                    <th>Sessions</th>
                                    <th>Total Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if(count($top_members) > 0) {
                                    $rank = 1;
                                    foreach($top_members as $member) {
                                        $hours = floor($member->total_minutes / 60);
                                        $mins = $member->total_minutes % 60;
                                        $medal = '';
                                        if($rank == 1) $medal = '🥇';
                                        elseif($rank == 2) $medal = '🥈';
                                        elseif($rank == 3) $medal = '🥉';
                                ?>
                                <tr>
                                    <td><?php echo $medal.' '.$rank; ?></td>
                                    <td><?php echo htmlentities($member->fname); ?></td>
                                    <td><span class="badge badge-primary"><?php echo $member->total_sessions; ?></span></td>
                                    <td><?php echo $hours.'h '.$mins.'m'; ?></td>
                                </tr>
                                <?php
                                        $rank++;
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='text-center'>No data available</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="tile">
                    <h4 class="tile-title"><i class="fa fa-calendar"></i> Monthly Breakdown (Last 6 Months)</h4>
                    <div class="tile-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Members</th>
                                    <th>Sessions</th>
                                    <th>Total Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if(count($monthly_data) > 0) {
                                    foreach($monthly_data as $month) {
                                        $month_name = date('F Y', strtotime($month->month.'-01'));
                                        $hours = floor($month->total_minutes / 60);
                                        $mins = $month->total_minutes % 60;
                                ?>
                                <tr>
                                    <td><?php echo $month_name; ?></td>
                                    <td><?php echo $month->unique_members; ?></td>
                                    <td><?php echo $month->total_sessions; ?></td>
                                    <td><?php echo $hours.'h '.$mins.'m'; ?></td>
                                </tr>
                                <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='text-center'>No data available</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Additional Stats -->
        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <h4 class="tile-title"><i class="fa fa-info-circle"></i> Additional Statistics</h4>
                    <div class="tile-body">
                        <div class="row">
                            <div class="col-md-4">
                                <p><strong>Average Session Duration:</strong></p>
                                <h4>
                                    <?php 
                                    if($overall_stats->avg_duration) {
                                        echo floor($overall_stats->avg_duration).' minutes';
                                    } else {
                                        echo '0 minutes';
                                    }
                                    ?>
                                </h4>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Total Gym Hours:</strong></p>
                                <h4>
                                    <?php 
                                    if($overall_stats->total_minutes) {
                                        echo floor($overall_stats->total_minutes / 60).' hours';
                                    } else {
                                        echo '0 hours';
                                    }
                                    ?>
                                </h4>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Today's Unique Visitors:</strong></p>
                                <h4><?php echo $today_stats->today_members ? $today_stats->today_members : 0; ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
    
    <script>
        // Weekly Attendance Chart
        const ctx = document.getElementById('weeklyChart').getContext('2d');
        const gradientMembers = ctx.createLinearGradient(0, 0, 0, 300);
        gradientMembers.addColorStop(0, 'rgba(102, 126, 234, 0.35)');
        gradientMembers.addColorStop(1, 'rgba(102, 126, 234, 0.05)');

        const gradientSessions = ctx.createLinearGradient(0, 0, 0, 300);
        gradientSessions.addColorStop(0, 'rgba(118, 75, 162, 0.45)');
        gradientSessions.addColorStop(1, 'rgba(118, 75, 162, 0.10)');

        const labels = <?php echo json_encode($chart_dates); ?>;
        const dataMembers = <?php echo json_encode($chart_members); ?>;
        const dataSessions = <?php echo json_encode($chart_sessions); ?>;

        const weeklyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        type: 'bar',
                        label: 'Total Sessions',
                        data: dataSessions,
                        backgroundColor: gradientSessions,
                        borderColor: 'rgb(118, 75, 162)',
                        borderWidth: 1,
                        borderRadius: 8,
                        barThickness: 'flex',
                        maxBarThickness: 28,
                        categoryPercentage: 0.6,
                        order: 2
                    },
                    {
                        type: 'line',
                        label: 'Unique Members',
                        data: dataMembers,
                        borderColor: 'rgb(102, 126, 234)',
                        backgroundColor: gradientMembers,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: 'rgb(102, 126, 234)',
                        pointHoverBackgroundColor: 'rgb(102, 126, 234)',
                        pointHoverBorderColor: '#ffffff',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        borderWidth: 3,
                        tension: 0.35,
                        fill: true,
                        order: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { usePointStyle: true, pointStyle: 'circle' }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(31, 41, 55, 0.92)',
                        titleColor: '#fff',
                        bodyColor: '#e5e7eb',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1,
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                const label = context.dataset.label || '';
                                const value = context.parsed.y;
                                return ' ' + label + ': ' + (Number.isFinite(value) ? value : 0);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { maxRotation: 0, autoSkip: true }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.06)' },
                        ticks: {
                            precision: 0
                        }
                    }
                },
                animations: {
                    tension: { duration: 800, easing: 'easeOutQuart' },
                    y: { duration: 800, easing: 'easeOutQuart' }
                }
            }
        });
    </script>
</body>
</html>
<?php } ?>
