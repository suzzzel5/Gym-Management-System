<?php
// Only show notifications for logged-in users
if(isset($_SESSION['uid']) && strlen($_SESSION['uid']) > 0) {
    $uid = $_SESSION['uid'];
    
    // Get recent bookings (last 7 days)
    $notification_sql = "SELECT t1.id as bookingid, t1.booking_date, t2.titlename as title, t2.Price 
                        FROM tblbooking as t1 
                        LEFT JOIN tbladdpackage as t2 ON t1.package_id = t2.id 
                        WHERE t1.userid = :uid 
                        AND t1.booking_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                        ORDER BY t1.booking_date DESC 
                        LIMIT 5";
    $notification_query = $dbh->prepare($notification_sql);
    $notification_query->bindParam(':uid', $uid, PDO::PARAM_STR);
    $notification_query->execute();
    $notifications = $notification_query->fetchAll(PDO::FETCH_OBJ);
    $notification_count = $notification_query->rowCount();
    
    if($notification_count > 0) {
?>
<!-- Notification Bar -->
<div class="notification-bar" id="notificationBar">
    <div class="container">
        <div class="notification-content">
            <div class="notification-icon">
                <i class="fas fa-bell"></i>
                <span class="notification-badge"><?php echo $notification_count; ?></span>
            </div>
            <div class="notification-text">
                <strong>Recent Bookings:</strong>
                <?php if($notification_count == 1): ?>
                    You have booked <strong><?php echo htmlentities($notifications[0]->title); ?></strong> 
                    on <?php echo date('M d, Y', strtotime($notifications[0]->booking_date)); ?>
                <?php else: ?>
                    You have <?php echo $notification_count; ?> active bookings in the last 7 days
                <?php endif; ?>
            </div>
            <div class="notification-actions">
                <a href="booking-history.php" class="btn-view-bookings">
                    <i class="fas fa-calendar-check me-1"></i> View All Bookings
                </a>
                <button class="btn-close-notification" onclick="closeNotificationBar()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Notification Bar Styles */
.notification-bar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 9999;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px 0;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    animation: slideDown 0.5s ease;
    transform: translateY(0);
    transition: transform 0.3s ease;
}

.notification-bar.hidden {
    transform: translateY(-100%);
}

@keyframes slideDown {
    from {
        transform: translateY(-100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.notification-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    flex-wrap: wrap;
}

.notification-icon {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    flex-shrink: 0;
}

.notification-icon i {
    font-size: 1.5rem;
    animation: bellShake 2s infinite;
}

@keyframes bellShake {
    0%, 50%, 100% {
        transform: rotate(0deg);
    }
    10%, 30% {
        transform: rotate(-10deg);
    }
    20%, 40% {
        transform: rotate(10deg);
    }
}

.notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ef4444;
    color: white;
    font-size: 0.75rem;
    font-weight: 700;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.notification-text {
    flex: 1;
    font-size: 1rem;
    line-height: 1.5;
    min-width: 250px;
}

.notification-text strong {
    font-weight: 700;
}

.notification-actions {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-shrink: 0;
}

.btn-view-bookings {
    background: white;
    color: #667eea;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    white-space: nowrap;
}

.btn-view-bookings:hover {
    background: #f8f9fa;
    color: #667eea;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.btn-close-notification {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-close-notification:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg);
}

.btn-close-notification i {
    font-size: 1.1rem;
}

/* Push header down when notification bar is visible */
body {
    padding-top: 80px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .notification-bar {
        padding: 12px 0;
    }
    
    .notification-content {
        gap: 15px;
    }
    
    .notification-icon {
        width: 40px;
        height: 40px;
    }
    
    .notification-icon i {
        font-size: 1.2rem;
    }
    
    .notification-text {
        font-size: 0.9rem;
        min-width: 100%;
        order: 2;
    }
    
    .notification-actions {
        order: 3;
        width: 100%;
        justify-content: space-between;
    }
    
    .btn-view-bookings {
        font-size: 0.85rem;
        padding: 8px 16px;
    }
}

@media (max-width: 480px) {
    .notification-text {
        font-size: 0.85rem;
    }
    
    .btn-view-bookings span {
        display: none;
    }
}
</style>

<script>
function closeNotificationBar() {
    const notificationBar = document.getElementById('notificationBar');
    notificationBar.classList.add('hidden');
    
    // Store in session storage that user closed it
    sessionStorage.setItem('notificationBarClosed', 'true');
    
    // Remove padding after animation
    setTimeout(() => {
        notificationBar.style.display = 'none';
        document.body.style.paddingTop = '0';
    }, 300);
}

// Check if user already closed the notification bar in this session
document.addEventListener('DOMContentLoaded', function() {
    if (sessionStorage.getItem('notificationBarClosed') === 'true') {
        const notificationBar = document.getElementById('notificationBar');
        notificationBar.style.display = 'none';
        document.body.style.paddingTop = '0';
    }
});
</script>

<?php
    }
}
?>
