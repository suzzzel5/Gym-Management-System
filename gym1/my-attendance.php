<?php session_start();
error_reporting(0);
require_once('include/config.php');

// Require user login
if (!isset($_SESSION['uid']) || strlen((string)$_SESSION['uid']) === 0) {
  header('Location: login.php');
  exit();
}

$uid = intval($_SESSION['uid']);
$msg = "";
$error = "";

// Ensure attendance table exists (in case admin hasn't opened attendance yet)
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
  $error = "Attendance system is not available right now.";
}

// Fetch user's attendance
$attendances = [];
$total_minutes_30 = 0;

// Progress model defaults (will auto-tune from history)
$target_weekly_sessions = 3;
$A = 0.0; $T = 0.0; $S = 0.0; $R = 0.0; $progress_score = 0;
$grade = 'C'; $next_action = '';

try {
  // Last 30 days total minutes
  $stats_sql = "SELECT COALESCE(SUM(duration_minutes),0) AS mins FROM tblattendance WHERE userid=:uid AND session_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND check_out_time IS NOT NULL";
  $stats_q = $dbh->prepare($stats_sql);
  $stats_q->bindParam(':uid', $uid, PDO::PARAM_INT);
  $stats_q->execute();
  $stats = $stats_q->fetch(PDO::FETCH_OBJ);
  $total_minutes_30 = $stats ? intval($stats->mins) : 0;

  // Weekly aggregates (last 12 weeks)
  $weekly_sql = "SELECT YEARWEEK(session_date,1) AS yw, COUNT(*) AS sessions, COALESCE(SUM(duration_minutes),0) AS minutes
                 FROM tblattendance 
                 WHERE userid=:uid AND session_date >= DATE_SUB(CURDATE(), INTERVAL 12 WEEK)
                 GROUP BY YEARWEEK(session_date,1)
                 ORDER BY yw DESC";
  $wq = $dbh->prepare($weekly_sql);
  $wq->bindParam(':uid', $uid, PDO::PARAM_INT);
  $wq->execute();
  $weekly_rows = $wq->fetchAll(PDO::FETCH_ASSOC);

  // Normalize into most-recent-first arrays
  $weekly_sessions = [];
  $weekly_minutes = [];
  foreach ($weekly_rows as $row) {
    $weekly_sessions[] = (int)$row['sessions'];
    $weekly_minutes[] = (int)$row['minutes'];
  }

  // Auto-tune target using median of last up to 8 recent weeks
  $for_target = array_slice($weekly_sessions, 0, min(8, count($weekly_sessions)));
  if (count($for_target) > 0) {
    $tmp = $for_target; sort($tmp);
    $n = count($tmp);
    $median = ($n % 2) ? $tmp[intdiv($n,2)] : (int)round(($tmp[$n/2-1] + $tmp[$n/2]) / 2);
    $target_weekly_sessions = max(2, min(5, $median));
  }

  // Compute Adherence A over last 4 weeks
  $nA = min(4, count($weekly_sessions));
  if ($nA > 0) {
    $sumA = 0.0;
    for ($i = 0; $i < $nA; $i++) {
      $sumA += min(1.0, $weekly_sessions[$i] / max(1, $target_weekly_sessions));
    }
    $A = $sumA / $nA; // 0..1
  }

  // Compute Streak S: consecutive recent weeks meeting target
  $streak = 0;
  for ($i = 0; $i < count($weekly_sessions); $i++) {
    if ($weekly_sessions[$i] >= $target_weekly_sessions) { $streak++; } else { break; }
  }
  $S = min(1.0, $streak / 4.0);

  // Compute Recency R: based on days since last session
  $last_sql = "SELECT DATEDIFF(CURDATE(), MAX(session_date)) AS days FROM tblattendance WHERE userid=:uid";
  $lq = $dbh->prepare($last_sql);
  $lq->bindParam(':uid', $uid, PDO::PARAM_INT);
  $lq->execute();
  $ld = $lq->fetch(PDO::FETCH_OBJ);
  $days = $ld && $ld->days !== null ? max(0, (int)$ld->days) : 9999;
  $R = exp(-$days / 7.0); // ~1 if very recent, decays with ~1 week half-life

  // Compute Trend T on minutes via simple linear regression over last up to 8 weeks
  $nT = min(8, count($weekly_minutes));
  if ($nT >= 2) {
    // Use most recent nT points (already most-recent-first)
    $ys = array_slice($weekly_minutes, 0, $nT);
    // Reverse to oldest->newest for stability
    $ys = array_reverse($ys);
    $xs = range(1, $nT);
    $mean_x = array_sum($xs)/$nT; $mean_y = array_sum($ys)/$nT;
    $num = 0.0; $den = 0.0;
    for ($i=0; $i<$nT; $i++) { $num += ($xs[$i]-$mean_x)*($ys[$i]-$mean_y); $den += ($xs[$i]-$mean_x)*($xs[$i]-$mean_x); }
    $slope = $den > 0 ? $num/$den : 0.0; // minutes per week
    // Map slope to 0..1 using sigmoid; k scales sensitivity
    $k = 0.02; // tuning: 50 minutes change ~ noticeable
    $sig = 1.0/(1.0 + exp(-$k * $slope));
    // Center so flat trend ~0.5, then scale to 0..1
    $T = $sig; // already 0..1 with 0 slope at 0.5
  } else {
    $T = 0.5; // neutral when insufficient data
  }

  // Final score
  $progress_score = (int)round(100 * (0.5*$A + 0.2*$T + 0.2*$S + 0.1*$R));
  if ($progress_score < 0) $progress_score = 0; if ($progress_score > 100) $progress_score = 100;

  // Grade and next action
  if ($progress_score >= 85) $grade = 'A';
  elseif ($progress_score >= 70) $grade = 'B';
  elseif ($progress_score >= 55) $grade = 'C';
  elseif ($progress_score >= 40) $grade = 'D';
  else $grade = 'E';

  $this_week = count($weekly_sessions) ? $weekly_sessions[0] : 0;
  if ($A < 0.7) {
    $needed = max(0, $target_weekly_sessions - $this_week);
    $next_action = $needed > 0 ? ("Aim for ".$needed." more session(s) this week.") : "Maintain your target this week.";
  } elseif ($R < 0.5) {
    $next_action = "Plan a session within the next 3 days.";
  } elseif ($T < 0.5) {
    $next_action = "Add ~15 minutes to two sessions next week.";
  } else {
    $next_action = "Great momentum — keep hitting ".$target_weekly_sessions." sessions/week.";
  }

  // Full history for table
  $sql = "SELECT id, session_date, check_in_time, check_out_time, duration_minutes, notes
          FROM tblattendance
          WHERE userid=:uid
          ORDER BY session_date DESC, check_in_time DESC";
  $query = $dbh->prepare($sql);
  $query->bindParam(':uid', $uid, PDO::PARAM_INT);
  $query->execute();
  $attendances = $query->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $e) {
  $error = "Unable to load your attendance right now.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>My Attendance | FIT TRACK HUB</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
    body { background: var(--light-color); padding-top: 90px; font-family: 'Inter', sans-serif; color: var(--text-primary); }
    .hero-section { background: var(--gradient-primary); color:#fff; padding:100px 0 60px; position:relative; overflow:hidden; }
    .hero-section::before { content:''; position:absolute; inset:0; background:url('img/hero-slider/1.png') center/cover no-repeat; opacity:.08; }
    .hero-title { font-family:'Poppins', sans-serif; font-weight:800; font-size:2.2rem; margin:0; text-shadow:2px 2px 4px rgba(0,0,0,.2); }
    .hero-subtitle { opacity:.9; margin-top:8px; }

    .stat-card { background:#fff; border-radius:20px; box-shadow:0 10px 40px rgba(0,0,0,.08); padding:22px; display:flex; align-items:center; gap:16px; }
    .stat-icon { font-size:28px; width:56px; height:56px; border-radius:14px; display:flex; align-items:center; justify-content:center; color:#fff; box-shadow:0 10px 25px rgba(0,0,0,.15); }
    .stat-icon.sessions { background: var(--gradient-primary); }
    .stat-icon.time { background: var(--gradient-secondary); }
    .stat-icon.last { background: var(--gradient-accent); }
    .stat-label { color: var(--text-secondary); font-weight:600; font-size:.9rem; }
    .stat-value { font-size:1.6rem; font-weight:800; color: var(--primary-color); }

    .card-wrapper { background:#fff; border-radius:20px; box-shadow:0 12px 40px rgba(0,0,0,.08); }
    .table thead th { background: #fff; border-bottom: 2px solid rgba(0,0,0,.06); color: var(--text-secondary); text-transform: uppercase; font-size:.8rem; letter-spacing:.5px; }
    .table tbody tr:hover { background: rgba(99,102,241,0.05); }
    .badge-status { border-radius:12px; padding:6px 10px; font-weight:600; }
  </style>
  <style>
    /* Progress bar */
    .progress-wrap { background:#eef2ff; border-radius:999px; overflow:hidden; height:14px; }
    .progress-bar { height:100%; width:0; transition: width .6s ease; }
    .progress-bar.ok { background: linear-gradient(90deg, #22c55e, #16a34a); }
    .progress-bar.warn { background: linear-gradient(90deg, #f59e0b, #d97706); }
    .progress-bar.bad { background: linear-gradient(90deg, #ef4444, #dc2626); }
    .breakdown small { color: var(--text-secondary); }

    /* Sparkline */
    .sparkline-card { background:#fff; border-radius:20px; box-shadow:0 12px 40px rgba(0,0,0,.08); padding:16px; }
    .sparkline-title { font-weight:700; }
  </style>
</head>
<body>
  <?php include 'include/header.php';?>

  <section class="hero-section">
    <div class="container">
      <h1 class="hero-title"><i class="fa fa-calendar-check me-2"></i>My Attendance</h1>
      <p class="hero-subtitle mb-0">Track your gym check-ins and session durations</p>
    </div>
  </section>

  <section class="section-padding" style="padding:50px 0;">
    <div class="container">
      <?php if($msg): ?>
        <div class="alert alert-success"><strong>Success:</strong> <?php echo htmlentities($msg);?></div>
      <?php endif; ?>
      <?php if($error): ?>
        <div class="alert alert-danger"><strong>Error:</strong> <?php echo htmlentities($error);?></div>
      <?php endif; ?>

      <div class="row g-4 mb-4">
        <div class="col-12">
          <div class="card-wrapper p-3 p-md-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
              <div>
                <h5 class="mb-1" style="font-weight:700;">Progress Score</h5>
                <div class="progress-wrap mt-2" aria-label="Progress">
                  <?php $cls = $progress_score>=70? 'ok' : ($progress_score>=40? 'warn':'bad'); ?>
                  <div class="progress-bar <?php echo $cls; ?>" style="width: <?php echo $progress_score; ?>%;"></div>
                </div>
                <div class="mt-2 d-flex align-items-center gap-3">
                  <span class="h4 mb-0" style="font-weight:800; color:var(--primary-color)"><?php echo $progress_score; ?>%</span>
                  <span class="badge <?php echo $progress_score>=70? 'bg-success' : ($progress_score>=40? 'bg-warning text-dark' : 'bg-danger'); ?>" style="border-radius:10px; padding:.35rem .6rem; font-weight:700;">Grade <?php echo $grade; ?></span>
                </div>
                <div class="text-muted mt-1" style="max-width:540px;"><?php echo htmlentities($next_action); ?></div>
              </div>
              <div class="breakdown d-flex flex-wrap gap-4">
                <div><div class="h6 mb-0">Adherence</div><small><?php echo round($A*100); ?>%</small></div>
                <div><div class="h6 mb-0">Trend</div><small><?php echo round($T*100); ?>%</small></div>
                <div><div class="h6 mb-0">Streak</div><small><?php echo round($S*100); ?>%</small></div>
                <div><div class="h6 mb-0">Recency</div><small><?php echo round($R*100); ?>%</small></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <?php
        // Prepare sparkline points for last up to 8 weeks (oldest to newest)
        $spark_points = '';
        $ws_old = array_reverse(array_slice($weekly_sessions, 0, min(8, count($weekly_sessions))));
        $npts = count($ws_old);
        $w = 260; $h = 60; $pad = 6;
        if ($npts >= 1) {
          $maxv = max(1, max($ws_old));
          $dx = $npts>1 ? ($w - 2*$pad) / ($npts - 1) : 0;
          for ($i=0; $i<$npts; $i++) {
            $x = $pad + $i * $dx;
            $y = $h - $pad - (($ws_old[$i]/$maxv) * ($h - 2*$pad));
            $spark_points .= ($i? ' ':'').round($x,1).','.round($y,1);
          }
        }
      ?>
      <div class="row g-4 mb-2">
        <div class="col-12">
          <div class="sparkline-card">
            <div class="d-flex justify-content-between align-items-center">
              <div class="sparkline-title">Last 8 Weeks Sessions</div>
              <div class="text-muted small">Target: <?php echo $target_weekly_sessions; ?>/week</div>
            </div>
            <div class="mt-2">
              <svg width="260" height="60" viewBox="0 0 260 60" role="img" aria-label="Weekly sessions sparkline">
                <polyline points="<?php echo $spark_points; ?>" fill="none" stroke="#6366f1" stroke-width="2" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4 mb-2">
        <div class="col-md-4">
          <div class="stat-card">
            <div>
              <div class="stat-label">Sessions (30 days)</div>
              <?php
                $count30 = 0;
                try {
                  $csql = "SELECT COUNT(*) c FROM tblattendance WHERE userid=:uid AND session_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND check_out_time IS NOT NULL";
                  $cq = $dbh->prepare($csql);
                  $cq->bindParam(':uid', $uid, PDO::PARAM_INT);
                  $cq->execute();
                  $crow = $cq->fetch(PDO::FETCH_OBJ);
                  $count30 = $crow ? intval($crow->c) : 0;
                } catch (PDOException $e) { $count30 = 0; }
              ?>
              <div class="stat-value"><?php echo $count30; ?></div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="stat-card">
            <div>
              <div class="stat-label">Total Time (30 days)</div>
              <div class="stat-value">
                <?php $h=floor($total_minutes_30/60); $m=$total_minutes_30%60; echo $h.'h '.$m.'m'; ?>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="stat-card">
            <div>
              <div class="stat-label">Last Check-in</div>
              <div class="stat-value">
                <?php echo !empty($attendances) ? date('M d, Y h:i A', strtotime($attendances[0]->check_in_time)) : '—'; ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card-wrapper p-3 p-md-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0" style="font-weight:700;">Attendance History</h5>
        </div>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>Date</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <th>Duration</th>
                <th>Status</th>
                <th>Notes</th>
              </tr>
            </thead>
            <tbody>
              <?php if(count($attendances) > 0): ?>
                <?php foreach($attendances as $a): ?>
                  <tr>
                    <td><?php echo date('M d, Y', strtotime($a->session_date)); ?></td>
                    <td><?php echo date('h:i A', strtotime($a->check_in_time)); ?></td>
                    <td>
                      <?php echo $a->check_out_time ? date('h:i A', strtotime($a->check_out_time)) : '<span class="badge bg-warning text-dark badge-status">Active</span>'; ?>
                    </td>
                    <td>
                      <?php 
                        if ($a->duration_minutes) {
                          $hh = floor($a->duration_minutes/60); $mm = $a->duration_minutes%60;
                          echo ($hh>0? $hh.'h ' : '').$mm.'m';
                        } else {
                          echo '-';
                        }
                      ?>
                    </td>
                    <td>
                      <?php echo $a->check_out_time ? '<span class="badge bg-success badge-status">Completed</span>' : '<span class="badge bg-warning text-dark badge-status">In Session</span>'; ?>
                    </td>
                    <td><?php echo htmlentities($a->notes); ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">
                    <i class="fa fa-inbox fa-2x mb-2"></i>
                    <div>No attendance records yet.</div>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <div class="text-muted small">Note: You cannot modify attendance from here. Contact admin for corrections.</div>
      </div>
    </div>
  </section>

  <script src="js/vendor/jquery-3.2.1.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
</body>
</html>
